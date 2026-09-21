# Background jobs (cron)

Some work is not done at a person's request but at a given time: publishing a scheduled article, sending notifications and the newsletter, retrying undelivered mail, the weekly backup.

## It works without any setup too

By default these jobs run **when the site is visited**. A site with ordinary traffic therefore does not need to set anything up. On a site that nobody visits at night, however, an article scheduled for 6:00 is published only with the first morning reader – and the newsletter goes out later than you wanted.

## Cron ensures exact timing

1. Open **Settings → System status**, section **Background jobs (cron)**.
2. Click **Create the cron address**. A ready-made line appears:

   ```
   */5 * * * * curl -s "https://www.example.com/ulohy?token=…" > /dev/null
   ```

3. Paste the line into the task scheduler (cron) in the hosting control panel. If the hosting does not want the whole command but only an address, enter just the address and an interval of **5 minutes**.

Hosting without cron can be replaced by any service that can call an address regularly (for example monitoring tools such as UptimeRobot).

The address contains a secret token. If it leaks, replace it with the **Create a new address (the old one stops working)** button; then remember to update the cron.

## How do I know it is running

**System status**, row **Background tasks**: it is fine when the jobs ran within the last 30 minutes. A warning means that cron is not calling the address, or that nobody has visited the site.

You can also open the address in a browser – it responds with the line `OK`, the time and a list of what was done.

## What exactly the jobs do

- publish scheduled articles and send their notifications (e-mail, Web Push, webhooks),
- send the next batch of a newsletter that is being delivered,
- retry sending mail that could not be delivered,
- create the weekly database backup and upload it off-site, if that is turned on.
