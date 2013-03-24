Golden Contact Computing - Box Boss Tool
-------------------

About:
-----------------
This tool helps with setting up boxes. Its intended to get any box in your standard main environments to be
up and running quickly. It's not meant to be an exhaustive list of installs for everything you'll ever need to
install (obviously)

Can be used to set up a Development Client, Development Server, Testing Server, or Production Server in minutes

... Staging/UAT is not missing from the list because in "Software my box has installed" terms it should be the
same as Production.

Furthermore, especially for Production, this is intended to be a quick solution to get you up and running and I
do not and would never recommend going into Production without checking things for yourself. In essence, I can
give you lots of tasty alcohol to help encourage your flow, but I can't hold it for you when you need to go.

So, there are basically only a few simple commands...

boxboss install cherry-pick - Pick individual or multiple bits of software to install
boxboss install autopilot - Use preset defaults to perform an unattended installation

boxboss install dev-client - Install a preconfigured set of software for a dev client (Your Workstation)
boxboss install dev-server - Install a preconfigured set of software for a dev server (Team Playaround Box)
boxboss install test-server - Install a preconfigured set of software for a Build/Testing server
boxboss install git-server - Install a preconfigured set of software for a Git SCM server
boxboss install production - Install a preconfigured set of software for a Production server (Public Server)

... which pretty much do as described ... all have the option of providing silent or autogen as a parameter.

The silent parameter will mean no questions at all will be asked, and defaults provided by boxboss will be
used. It is, of course down to you to change them. At the end of the install, the users, passwords, install
folders and other install data will be provided to you as screen output.

The autogen parameter will mean no questions at all will be asked, and defaults provided by boxboss will be
used for non-sensitive data, like install folders. Sensitive data such as passwords will be autogenerated
as needed. It is, of course down to you to change them if you want to. It is also down to you to save the
output. At the end of the install, the users, passwords, install folders and other install data will be
provided to you as screen output.



Installation
-----------------

To install boxboss cli on your machine do the following. If you already have php5 and git installed skip line 1:

apt-get php5 git

git clone https://github.com/phpengine/boxboss && sudo php boxboss/install-silent

or...

git clone https://github.com/phpengine/boxboss && sudo php boxboss/install
(If you want to choose the install directory)

... that's it, now the boxboss command should be available at the command line for you.

-------------------------------------------------------------

Available Commands:
---------------------------------------

install

        - dev-client
        install a dev client machine for you to work on, a bunch of IDE's, DB's and a complete set of the
        tools you need to start work immediately. You can use the keyword silent which will use boxboss's
        default values to perform an unattended install. You can also use the keyword autogen which will
        generate values for sensitive data to perform an unattended install.
        example: boxboss install dev-client *user*
        example: boxboss install dev-client *user* silent
        example: boxboss install dev-client *user* autogen

        - dev-server
        Install the preconfigured list of software for a developers server. Specifying a user to install software
        as is required. You can use the keyword silent which will use boxboss's default values to perform an
        unattended install. You can also use the keyword autogen which will generate values for sensitive data
        to perform an unattended install.
        example: boxboss install dev-server *user*
        example: boxboss install dev-server *user* silent
        example: boxboss install dev-server *user* autogen

        - test-server
        Install the preconfigured list of software for a testing server. Specifying a user to install software
        as is required. You can use the keyword silent which will use boxboss's default values to perform an
        unattended install. You can also use the keyword autogen which will generate values for sensitive data
        to perform an unattended install.
        example: boxboss install test-server *user*
        example: boxboss install test-server *user* silent
        example: boxboss install test-server *user* autogen

        - autopilot
        Perform an installation, or multiple installations of software, using the configurations and options
        provided in an autopilot file.
        example: boxboss install autopilot *autopilot-file*

        - cherry-pick
        Install indivdual available programs
        example: boxboss install cherry-pick *user*
