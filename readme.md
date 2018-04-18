**Code test** **@wonderkind** 

### Introduction

The application is built on Laravel 5.6 with a few dependencies (**thujohn/twitter** for Twitter API) and **predis/predis**.
On the main page there is a simple interface containing a single form with a user input field. During the API calls, the information is updated in the database after each API call, this way we can inspect the entire process.

A screenshot of retrieved data : 

![ui](/screenshots/ui.png)

### Requirements

A working redis and cron instance. Also, this application won't work if the Laravel queue worker won't be started : 

`php artisan queue:work`

The cron scheduler too : 

`* * * * * php /project/artisan schedule:run >> /dev/null 2>&1`

A screenshot of queue worker after processing a few requests : 

![worker](/screenshots/worker.png)

### Authorization

In order to perform API calls, four tokens should be provided (consumer and access keys). They're stored in the .env file.

### Twitter API and limitations

Twitter puts a fixed amount of API calls per 15 min interval limit. As for **retweets** endpoint, only **75 or 300** (depending on authorisation type) calls are allowed within 15 minutes. Also, taking in consideration that most of API calls won't return more than 100 records per call, we need to split the requests in order to get full information regarding a specific tweet.
Since requesting data from API can take a lot of time, in this example a special queueable job will perform this action. It will be launched in the foreground on the server, through redis, thus it won't freeze the user interface. 

### Application workflow

After a valid tweet url was passed, the application will dispatch the job to the queue. 

First API call, endpoint (statuses/show/:id). This will return basic tweet information (text, user, retweets count).

Second API call, endpoint (statuses/retweets/:id). The response may contain up to 100 retweets on the requested tweet id.
Despite of the lack of documentation, this endpoint supports max_id parameter which is used as paging operator to get older records.
The application will iterate over the pages until reaches to the end. The sum of followers will be updated in the database.

### Database structure

There are two main tables `tweets` and `twitter_users` with corresponding information. There is a one-to-many relation between a Twitter User and his Tweets.

![database](/screenshots/database.png)

### What was implemented?

POST requests are validated, especially the main form request is passing through a custom validation rule which validates a tweet url. 
Every 2 hour, each tweet in the database is going to be updated automatically. There is a defined schedule (App\Console\Kernel) which will iterate through all the tweets and dispatch them to the updater job if they haven't been updated for more than 2 hours. 
As mentioned above, the API calls are performed on a separate instance, all queued. The concurrency may be solved using different priorities for the job worker, depending on the requests and the queue load. 

