# Lazy Cron

## The idea

The idea behind this plugin is related to Moodle sites hosted on cloud with variable usage patterns.
As we all know Moodle cron is by default configured to be executed every minute in order to provide correct backend
functionality to the system. In most cases cron actions directly depend on the presence of recent user activity. 
If there are none cron will still be executed but nothing worhtwile will happen. On self-hosted in-house instances that
may not matter much, but it does in commercial cloud environments where infrastructure is commodity and anything that 
can be billed is being billed.

If you desire to optimize your cloud provider costs it may be interesting to consider using this plugin.

## What it does

This plugin replaces standard moodle cron offering a new CLI tool that behaves exactly the same as the core cron with
some important differences.

Plugin offers two new settings through which user can configure execution schedule.
The first one is `time passed since last user login`.  
If no user logged in instance in timeframe configured in the plugin - do not execute cron.
Second setting is `time passed since last cron execution`. If cron was not executed for period longer than configured in 
this setting - execute cron regardless of the fact if there were user activity or not.

### Moodle 3.11+ behavior

If you install this on the instance running Moodle 3.11 or more recent there is a new setting displayed to the user.
We can configure scheduled task override. This is for cases where there may be scheduled tasks that we want execute 
**EVERY TIME** cron is executed regardless of the lazy cron configuration. This functionality depends on the
`$CFG->scheduled_tasks` override introduced in Moodle 3.11.

## Installation

Plugin is implemented as Moodle admin tool so make sure to deploy files into `<moodle>/admin/tool/lazycron`. You should
not use standard cron and lazy cron at the same time.

After installing the plugin by default lazy cron is not enabled. To enable it you need to go to the plugin settings page
and check settings `Enabled`. To finish enabling the functionality configure OS cronjob or systemd timer as outlined 
below in this document.

## OS configuration

On Linux/Unix type of systems you can install the cron as cronjob or as systemd service.

### Cronjob setup

Generic template
```
* * * * *    webserveruser    php /path/to/moodle/admin/tool/lazycron/cli/cron.php >/dev/null
```

Create cronjob file as the one we show here and place it in `/etc/cron.d/` you can name it any way you want, for example
`sitename-lazycron`.

### Systemd setup

On installations with systemd it is preferable to configure custom service and timer to execute it. Templates are as
follows:

service unit template (we assume the filename `lazycron.service`)
```
[Unit]
Description=Execute Moodle lazy cron

[Service]
Type=exec
ExecStart=php -f /path/to/moodle/admin/tool/lazycron/cli/cron.php
User=webserveruser
Group=webservergrouop

[Install]
WantedBy=multi-user.target
```
Adjust path to php executable, path to Moodle source files, web server user and group


service timer template (we assume the filename `lazycron.timer`)
```
[Unit]
Description=Run Moodle lazy cron

[Timer]
OnBootSec=10min
OnCalendar=*-*-* *:*:00
Persistent=true
Unit=lazycron.service

[Install]
WantedBy=timers.target
```

Once you have the files ready install them correctly. Here is the procedure:

```shell
sudo -- cp lazycron.service '/etc/systemd/system/'
sudo -- cp lazycron.timer '/etc/systemd/system/'
sudo -- systemctl daemon-reload
sudo -- systemctl enable --now lazycron.timer
```

## Disable lazy cron

If you just want to quickly disable cron execution without modifying OS configuration visit the plugin settings page
and uncheck `Enabled` setting.

If you want to completely disable lazy cron delete cronjob or disable systemd timer.

```shell
sudo -- rm -f /etc/cron.d/sitename-lazycron
```

```shell
sudo -- systemctl stop lazycron.timer
sudo -- systemctl disable lazycron.timer
sudo -- systemctl daemon-reload
sudo -- systemctl reset-failed
sudo -- rm -f /etc/systemd/system/lazycron.*
```

## ICON

[Lazy icons created by Paul J. - Flaticon](https://www.flaticon.com/free-icons/lazy)

## Copyright

(c) 2024 [Moodle US][moodleus-site]

Code for this plugin is licensed under the [GPLv3 license][GPLv3].

[GPLv3]: http://www.gnu.org/licenses/gpl-3.0.html "GNU General Public License"
[moodleus-site]: https://moodle.com/us "Moodle US"
