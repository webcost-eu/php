<?php use Carbon\Carbon;

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tasks_cron
 *
 * @property-read Magreement_tasks $magreement_tasks
 * @property-read Mcontact_meetings $mcontact_meetings
 * @property-read Magreement_emails $magreement_emails
 * @property-read Mgoogle_events $mgoogle_events
 * @property-read Msynchronizations $msynchronizations
 * @property-read Google_calendar $google_calendar
 * @property-read Muser $muser
 */
class Calendar_cron extends Cron_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model([
            'magreement_tasks',
            'mcontact_meetings',
            'magreement_emails',
            'mgoogle_events',
            'msynchronizations',
            'muser'
        ]);

        $this->load->library('google_lib');
        $this->load->library('google_calendar');
        $this->logger->info('Calendar cron');
    }

    public function index()
    {
        //$this->remove_expired();
        $this->link_events();
        $this->remove_sync();
        $this->watch();
    }
    
    /**
     * cron: Remove expired synchronization channel | manual
     */
    public function remove_expired() {
        $this->logger->info('Remove expired: start');
        $start = 0;

        while ($channels = $this->msynchronizations->fetchAll([
            'expired_at<=' => Carbon::now('UTC')],
            [],
            ['start' => $start, 'count' => $this->limit])) {
            $sql = $this->db->last_query();
            $this->logger->info('Channel qury: ' . $sql);
            foreach ($channels as $channel) {
                $this->logger->info('Channel: ' . $channel->id);

                if (!($this->google_calendar->stopWatch($channel))) {
                    $this->logger->info('Channel stopping error: ' . $this->google_calendar->getError());
                }
            }
            $start += $this->limit;
        }
        $this->logger->info('Remove expired: end');
    }
    
    /**
     * Cron: watch user calendars if no sync record in db | every hour
     */
    public function watch()
    {
        $this->logger->info('Watch: start');
        $start = 0;

        /* set empty data to null*/
        $this->muser->update(['google_calendar_access_data' => null], ['google_calendar_access_data' => '']);

        while ($users = $this->muser->fetchAll([
            'google_calendar_access_data is not null' => null
        ], [], ['start' => $start, 'count' => $this->limit])) {
            foreach ($users as $user) {
                $this->logger->info('User: ' . $user->id);
                if (!google_lib()->setUser($user)) {
                    continue;
                }

                $syncWhere = [
                    'user_id' => $user->id,
                    'calendar_id' => $user->google_calendar_id
                ];

                if ($this->msynchronizations->countRows($syncWhere)) {
                    $this->logger->info('Already watched.', $syncWhere);
                    continue;
                }

                $this->logger->info('Watch user: ' . $user->id);
                if (!($this->google_calendar->watch())) {
                    $this->logger->info('Watch error: user ' . $user->id . ': ' . $this->google_calendar->getError());
                }
            }
            $start += $this->limit;
        }
        $this->logger->info('Watch: end');
    }

    /**
     * Cron: remove today expiration channel and create new | every hour
     */
    public function remove_sync()
    {
        $this->logger->info('ReWatch: start');
        $start = 0;

        while ($channels = $this->msynchronizations->fetchAll([
            'expired_in_hours' => 2],
            [],
            ['start' => $start, 'count' => $this->limit])) {
            $sql = $this->db->last_query();
            $this->logger->info('Channel qury: ' . $sql);
            foreach ($channels as $channel) {
                $this->logger->info('Channel: ' . $channel->id);

                if (!($user = $channel->withUser(false))) {
                    $this->msynchronizations->delete(['id' => $channel->id]);
                    $this->logger->info('Channel without user: ' . $channel->id);
                    continue;
                }

                if (!(google_lib()->setUser($user))) {
                    continue;
                }

                if (!($this->google_calendar->stopWatch($channel))) {
                    $this->logger->info('Channel stopping error: ' . $this->google_calendar->getError());
                }

                $syncWhere = [
                    'user_id' => $user->id,
                    'calendar_id' => $user->google_calendar_id
                ];

                if ($this->msynchronizations->countRows($syncWhere)) {
                    $this->logger->info('Already watched');
                    continue;
                }

                $this->logger->info('Watch user: ' . $user->id);
                if (!($this->google_calendar->watch())) {
                    $this->logger->info('Watch error: ' . $this->google_calendar->getError());
                }
            }
            $start += $this->limit;
        }
        $this->logger->info('ReWatch: end');
    }

    /**
     * cron: link google events with crm events | every min
     */
    public function link_events()
    {
        $this->logger->info('link events: start');
        $start = 0;

        while ($googleItems = $this->mgoogle_events->fetchAll(['pivot_table is null' => null], [], ['start' => $start, 'count' => $this->limit])) {
            foreach ($googleItems as $googleEvent) {
                $this->logger->info('try: ' . $googleEvent->google_id);
                $pivot_table = false;
                if ($item = $this->magreement_tasks->fetchOne(['google_event_id' => $googleEvent->google_id])) {
                    $pivot_table = TABLE_AGREEMENT_TASKS;
                } else if ($item = $this->mcontact_meetings->fetchOne(['google_event_id' => $googleEvent->google_id])) {
                    $pivot_table = TABLE_CONTACT_MEETINGS;
                } else if ($item = $this->magreement_emails->fetchOne(['google_event_id' => $googleEvent->google_id])) {
                    $pivot_table = TABLE_AGREEMENT_EMAILS;
                }

                if ($pivot_table) {
                    $this->logger->info('linked to: ' . $pivot_table . ' id: ' . $item->id);
                    $this->mgoogle_events->update(['pivot_table' => $pivot_table, 'pivot_id' => $item->id]);
                }
            }
            $start += $this->limit;
        }

        $this->logger->info('link events: end');
    }

    /**
     * Cron: remove all channels | manual run if need to remove channels
     */
    public function remove_all()
    {
        $this->logger->info('Remove all: start');
        $start = 0;

        while ($channels = $this->msynchronizations->fetchAll([], [], ['start' => $start, 'count' => $this->limit])) {
            foreach ($channels as $channel) {
                $this->logger->info('Channel: ' . $channel->id);

                if (!($user = $channel->withUser(false))) {
                    $this->msynchronizations->delete(['id' => $channel->id]);
                    $this->logger->info('Channel without user: ' . $channel->id);
                    continue;
                }

                if (!google_lib()->setUser($user)) {
                    continue;
                }

                if (!($this->google_calendar->stopWatch($channel))) {
                    $this->logger->info('Channel stopping error: ' . $this->google_calendar->getError());
                }
            }
            $start += $this->limit;
        }
        $this->logger->info('Remove all: end');
    }
}
