# The project is written in Сodeigniter 3
<b>original</b> - initial project code
<br/><b>after_refactor</b> - the result of project optimization and refactoring

## Problems that have been resolved.
### 1. Rights, access and roles
#### It was:
The rights and roles were organized by dividing the project structure into appropriate directories (admin, agent, consultant, solicitor). Because of this, there was a lot of duplicated code, making it difficult to make changes to existing code and write new functionality. When adding a new role, it was necessary to copy the corresponding functionality, which entailed even more time costs for further maintenance of the project
#### Made:
A functionality has been created based on the matrix of user membership in a role and role access to functionality, which allows checking the role and access to certain functions inside the controller. Controllers (admin/chat, agent/chat, admin/dashboard, agent/dashboard, etc.) are combined into chat, dashboard, etc., respectively. This removed code duplication. The time for making edits and creating new functionality has been reduced significantly. When creating a role, it is enough to give it the necessary rights, and not copy the code.

### 2. Core back
#### It was:
There was a single controller, there was a universal model for data retirieving. When making changes and creating new functionality, they accumulated extra code that was needed in one place, but not needed in another. The files grew to large sizes, making them difficult to understand.
#### Made:
The base controller has been optimized, the base model has been optimized. Thematic controllers (agent, chat, etc.) have been created, which contain only the code necessary for the entity. This allowed us to reduce the time for understanding the functionality and its maintenance.

### 3. Core front
#### It was:
Each page of the site contained all the HTML markup. There were also many js scripts right in the markup. Duplication of html for each role, as well as for the controller. html markup was also found in the php code. To make edits, it was necessary to correct a lot of files, which entailed very large time costs.
#### Made:
The template framework has been created. All templates of the same type are combined into one. Added differentiation by roles. html has been broken down into logical parts. This helped to reduce the time for editing templates.

### 4. Localization.
#### It was:
Since the client wanted to be able to edit the texts, they were stored in a database. Some of the texts were stored directly in the code (php and html), which is unacceptable. The text was translated through a helper function using an index (translate(13)) from the database. This made it difficult to work with templates and translations. As a result, it created a lot of garbage in the database, as well as a lot of duplicated data"
#### Made:
Made harmonization of standard localization tools and localization on the fly. Data can be edited both on the site and through standard tools. What is the priority of the data in the database. Those. when a new tag appears in the translation files, it is added to the database, but when it is changed, it is not.

### 5. working with data
#### It was:
The principles of the OOP were violated. Although a universal class was created for working with data, there were queries right in the code. Data retrieving were scattered throughout the code. This caused difficulties when making changes to the structure of the database, as well as to the functionality
### Made:
A basic model has been created. Created thematic models (chat, user, agent, etc.) designed to work with the relevant data. Added work with objects. The code has become cleaner, more readable and more understandable. Reduced time to understand and maintain the code.

### What else has been done
- Added theme styling (change font, font size, background, colors and etc. via admin panel)
- Integration with google calendar (add event to calendar and fetch events from calendar to CRM)
- Integration with SMSApi - send sms to phone number
- Optimization of the database (creation of keys, indexes)
