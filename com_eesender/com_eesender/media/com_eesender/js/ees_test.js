$(document).ready(function(e) {

    $("#invalid_email").addClass('hide');

    $("#sendTest").click(function () {
        $('#send-test-log').html('');
        $('#send-test-transactionid').html('');
        $('#test-status-error-msg').html('');
        $('#test-status').html('');
        $('#contact-test-status').html('');

        var name = $("#name").val();
        var email = $("#emailAddress").val();
        var textArea = $("#textArea").val();

        if (IsEmail(email) == false) {
            $("#invalid_email").removeClass('hide');
            $("#invalid_email").text('Invalid email');
            return false;
        }

        $("#invalid_email").addClass('hide');

        var subject = 'Subject';
        var channel = 'Test';

        sendTest();


        function sendTest() {
            $.post("https://api.elasticemail.com/v2/email/send?", {
                    apikey: Joomla.getOptions( 'apikey'),
                    subject: subject,
                    from: Joomla.getOptions('username'),
                    fromName: name,
                    to: email,
                    channel: channel,
                    bodyText: textArea
                },
                function (response) {

                    if (response.success) {
                        $('#send-test-log').html('Send: <span class="test-ok">OK</span>');
                        $('#send-test-transactionid').html('Message ID: <span>' + response.data.messageid + '</span>');
                        $('#loader').removeClass('hide');

                        setTimeout(function () {
                            sendStatus(response);
                        }, 8000);

                    } else {
                        $('#send-test-log').html('Send: <span class="test-error">' + response.error + '</span>');
                    }

                }
            )
            .fail(function(){
                $('#send-test-log').html('Error: <span class="test-error">' + 'Most likely that there is an issue with the internet conection' + '</span>');
            });

        }

        function sendStatus(response) {

            $.post("https://api.elasticemail.com/v2/email/status?", {
                    apikey: Joomla.getOptions( 'apikey'),
                    messageID: response.data.messageid
                }, function (data) {
                    
                    if (data.success === true) {
                        var status = '';

                        switch (data.data.status) {
                            case 1:
                                status = 'Ready To Send';
                                break;
                            case 2:
                                status = 'Waiting To Retry';
                                break;
                            case 3:
                                status = 'Sending';
                                break;
                            case 4:
                                status = 'Error';
                                break;
                            case 5:
                                status = 'Sent';
                                break;
                            case 6:
                                status = 'Opened';
                                break;
                            case 7:
                                status = 'Clicked';
                                break;
                            case 8:
                                status = 'Unsubscribed';
                                break;
                            case 9:
                                status = 'Abuse Report';
                                break;
                            default:
                                status = '---';
                        }

                        $('#test-status').html('Email status: <span>' + status + '</span>');
                        if (data.data.errormessage !== '') {
                            $('#test-status-error-msg').html('Error message: <span>' + data.data.errormessage + '</span>')
                        }
                        ;

                        setTimeout(function () {
                            contactStatus();
                        }, 1000);

                    } else {
                        $('#test-status').html('Email error: <span class="test-error">' + data.error + '</span>');
                    }
                }
            )            
            .fail(function(){
                $('#send-test-log').html('Error: <span class="test-error">' + 'Most likely that there is an issue with the internet conection' + '</span>');
            });
        }

        function contactStatus() {
            $.post("https://api.elasticemail.com/v2/contact/loadcontact?", {
                    apikey: Joomla.getOptions( 'apikey'),
                    email: email
                }, function (resp) {

                    if (resp.success) {

                        var contactStatus = '';

                        switch (resp.data.status) {
                            case -2:
                                contactStatus = 'Transactional';
                                break;
                            case -1:
                                contactStatus = 'Engaged';
                                break;
                            case 0:
                                contactStatus = 'Active';
                                break;
                            case 1:
                                contactStatus = 'Bounced';
                                break;
                            case 2:
                                contactStatus = 'Unsubscribed';
                                break;
                            case 3:
                                contactStatus = 'Abuse';
                                break;
                            case 4:
                                contactStatus = 'Inactive';
                                break;
                            case 5:
                                contactStatus = 'Stale';
                                break;
                            case 6:
                                contactStatus = 'NotConfirmed';
                                break;
                            default:
                                contactStatus = '---';
                        }

                        $('#contact-test-status').html('Contact status: <span>' + contactStatus + '</span>');
                    } else {
                        $('#contact-test-status').html('Contact error: <span class="test-error">' + resp.error + '</span>');
                    }
                    $('#loader').addClass('hide');
                }
            )            
            .fail(function(){
                $('#send-test-log').html('Error: <span class="test-error">' + 'Most likely that there is an issue with the internet conection' + '</span>');
            });
        }

        return false;
    });

    function IsEmail(email) {
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!regex.test(email)) {
            return false;
        }
        return true;
    }

});