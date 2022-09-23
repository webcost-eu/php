<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0">

	<style>
        .recipient_address {
            position: absolute;
            width: 82mm;
            height: 37mm;
            bottom: 15mm;
            right: 20mm;
            padding: 4mm;
        <? if (ENVIRONMENT != 'production') { ?> border: 1px solid black;
            border-radius: 10px;
        <? } ?>
        }
	</style>
</head>


<body>

<div class="sender_address">
    <?= nl2br($sender_address) ?>
</div>

<div class="recipient_address">
    <?= nl2br($recipient_address) ?>
</div>

</body>
</html>
