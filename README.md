# conv-message-ids
Simple command-line tool to map SparkPost webhooks "message_id" format into the format (currently) used for SMTP injection responses, and which appear in delivered message headers as a Message-ID: header.
Note that this SparkPost behaviour is not officially documented and may be subject to change at any time without notice.