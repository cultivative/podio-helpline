# Podio Helpline

![GitHub repo size](https://img.shields.io/github/repo-size/cultivative/podio-helpline)
![GitHub contributors](https://img.shields.io/github/contributors/cultivative/podio-helpline)
![GitHub stars](https://img.shields.io/github/stars/cultivative/podio-helpline?style=social)
![GitHub forks](https://img.shields.io/github/forks/cultivative/podio-helpline?style=social)
![Twitter Follow](https://img.shields.io/twitter/follow/cultivative?style=social)

Podio Helpline is a custom solution that allows neighborhood help groups to manage incoming calls & emails, the relationships with the people they serve and their pool of volunteers.

This project is the open-source code behind the implementation originally created for https://sunsethelpers.org. 

## Please note: If you are a mutual aid group
We, the nonprofit [Cultivative](https://cultivative.org), can help with the Podio setup and provide your with our hosted webservice. 
You will not need to complete the installation described below in that case. Please contact [support@cultivative.org](mailto:support@cultivative.org)</a> to get in touch.

## Prerequisites

Before you begin, ensure you have met the following requirements:
* You have a Podio account and organization.
* You have a Google Voice account with a phone number to be exposed.
* You have a dedicated email address that you can forward to be exposed.

## Installing Podio Helpline

Using Composer (recommended)

`composer require cultivative/podio-helpline`


## Configure Podio Helpline

To configure Podio Helpline with its own self-hosted webservice, follow these steps:

1. Install the Podio apps (Requests, People, Volunteers) either manually or via the app store.
2. Configure Google Voice to send calls to voicemail and via forward to the Request app's intake email in Podio.
3. Configure your email address to forward all mails to the Request app's intake email in Podio.
3. Copy '/config/podio-settings-example.php' file to '/config/podio-settings.php' and update it with your Podio settings.
4. Deploy the php code and expose '/src/request/' it as the endpoint for the web service.
5. Add your endpoint URL followed by '?space_id=' and the id of your Podio workspace as 'create item' web hook:

## Using Podio Helpline

To use Podio Helpline, follow these steps:

1. Incoming calls and emails come automatically to the Request app in Podio.
2. The companion webservice automatically cleanses the requests.
3. If this is a new caller a new person will be created in the People app in Podio.
4. For existing callers the request will automatically be linked to the existing person in the database
5. Once the webservice processed the new request it alerts the workspace users ("Screeners") via Podio   
2. Screeners will manage the requests, relationships and volunteers via the Podio workspace

## Contributing to Podio Helpline

To contribute to Podio Helpline, follow these steps:

1. Fork this repository.
2. Create a branch: `git checkout -b <branch_name>`.
3. Make your changes and commit them: `git commit -m '<commit_message>'`
4. Push to the original branch: `git push origin <project_name>/<location>`
5. Create the pull request.

Alternatively see the GitHub documentation on [creating a pull request](https://help.github.com/en/github/collaborating-with-issues-and-pull-requests/creating-a-pull-request).

## Contact

If you want to contact me you can reach me at [support@cultivative.org](mailto:support@cultivative.org).

## License

This project uses the following license: [GNU General Public License v3.0](https://github.com/cultivative/podio-helpline/blob/master/LICENSE).
