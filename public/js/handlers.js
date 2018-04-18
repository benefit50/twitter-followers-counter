$(function(){

    $('button.fetchButton').on('click', function () {

        $.ajax({
            url: '/registerTask',
            type: 'POST',
            dataType: 'json',
            data: {
                'url': $('.mainForm').find('input[name=url]').val()
            },
            success: function (data) {

                var _data = {};
                $('.tweetData').find('.sourceAlert').text('').hide();

                if (data.hasOwnProperty('in_progress')) {
                    _data.text = 'In progress...';
                    _data.author = 'In progress...';
                    _data.followers = '...';

                    var timer = new TweetInsightsTimer();
                    timer.setTweetId(data.tweet);
                    timer.startTimer();
                } else {
                    _data.text = data.text;
                    _data.author = '@' + data.user.screen_name;
                    _data.followers = 'Retweeters followers count : ' + data.retweets_followers;

                    $('.tweetData').find('.sourceAlert').show().text('Finished. Source: database cache');
                }

                $('.tweetData').show().find('.card-title').text(_data.text);
                $('.tweetData').find('.card-subtitle').text(_data.author);
                $('.tweetData').find('.card-text').text(_data.followers);

                $('.errorMessages').hide();
            },
            error: function (data) {
                var error = data.responseJSON.errors.url[0];
                $('.errorMessages').show().find('li').text(error);
            }
        });

    });

    /**
     * @constructor
     */
    function TweetInsightsTimer() {
        this.interval = 500;
        this.url = '/getTweetDetails';
        this.tweetId = '';
        this.active = false;
    }

    /**
     * @param tweetId
     */
    TweetInsightsTimer.prototype.setTweetId = function (tweetId) {
        this.tweetId = tweetId;
    };

    /**
     * Timer-function used to refresh the data on page gradually until all statistic is collected
     */
    TweetInsightsTimer.prototype.cycle = function () {

        var that = this;

        $.ajax({
            url: that.url,
            type: 'POST',
            dataType: 'json',
            data: {
                'tweet': that.tweetId
            },
            success: function (data) {
                if (data.hasOwnProperty('text')) {
                    $('.tweetData').show().find('.card-title').text(data.text);
                    $('.tweetData').find('.card-subtitle').text('@' + data.user.screen_name);
                    $('.tweetData').find('.card-text').text('Retweeters followers count : ' + data.retweets_followers);
                }

                if (data.status === 1) {
                    that.stopTimer();
                    $('.tweetData').find('.sourceAlert').show().text('Finished. Source: api');
                }
            }
        });

        if (that.active)
            setTimeout(function () {
                that.cycle()
            }, that.interval);
    };

    TweetInsightsTimer.prototype.startTimer = function () {
        this.active = true;
        this.cycle();
    };

    TweetInsightsTimer.prototype.stopTimer = function () {
        this.active = false;
    };

});