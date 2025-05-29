# CompClub-Hub

This project is brought to you by:
Imogen 
Safica 
Jasmine 


To run this project, Apache and PHP must be installed (preferably use XAMPP stack).


CompClubHub is a web application that will help club admins to manage their clubs and their club competitions.

Files to take note of:
loginDetails.txt - contains user and admin login details currently stored in this project's database
compclubhub.sql - contains the sql database creation script of CompClubHub
updated erm.jpg - the updated entity relationship model

Folder to take note of:
commonPhp - contains login and logout headers depending on if the user is logged in or not
config - contains the file to initiate the connection to the database
css - external styling
imgs - images used



Using CompClubHub:

You can access our web application at 127.0.0.1/compclubhub/index.php if you have downloaded and extracted the compclubhub folder in htdocs of xampp.



General features that can be accessed without logging in:

On index.php is the club search and the club category leaderboard search:
    - club_search.php - returns details relating to a club you have searched for
    - club_category_leaderboard_search.php - returns details relating to a club category you have searched for, ranking
        users by their score according to the clubs they are part of that equal the category searched for

You may also access competition pages (will only show available data depending on current status of competition and rounds) 
and club pages (not fully working).

You can also click the login button and the register button on the form to create a new user account if needed.



Logging in:

To login, click the LOGIN button on the top right hand corner of your screen OR navigate to login.php.



User features:

Once logged in as a user, you will be directed to your user dashboard.

Here, you can view and edit your user details, (soft) delete your user account, view your ongoing (only works for round 1s) competitions 
and your past competitions, and your membership details (the clubs you are part of and etc.).

You also have an option to create an admin account to manage a new club you choose to create. If you do this, you will have to
logout and login as your newly created admin account to access admin features.



Admin features: 

Once logged in as an admin, you will be directed to your admin dashboard.

Here, you can view and edit your admin details, club details, view your clubs ongoing and past competitions,
view your club members, and create competitions for your club.



Creating a competition:

CompClubHub only works with competitions that have only 2 rounds.
If you create a competition with more than 2 rounds, it will automatically be ended after the 2nd round.

As of now, you can only complete a competition if you create and edit competition details in one go.

The order in which php files are accessed during creating and editing a competition is as follows:
create_competition.php
comp_1st_round.php (will generate round1_competitionID.json)
edit_round1_results.php
submit_round1_results.php (will generate r1_results_competitionID.json)
comp_2nd_round.php (will generate round2_competitionID.json)
edit_round2_results.php
submit_round2_results.php (will generate r2_results_competitionID.json)
finish_competition.php (will generate final_competitionID.json)
