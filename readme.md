BACKUP YOUR DATA AND FILES before upgrading, really, please.
There are details at https://community.opmantek.com/display/OA/Home

# Open-AudIT

Open-AudIT is an application to tell you exactly what is on your network, how it is configured and when it changes. Open-AudIT will run on Windows and Linux systems. Open-AudIT is a database of information, that can be queried via a web interface and JSON API. Data about the network is inserted using audit scripts (shell, vbscript) as well as Nmap and SNMP results. The entire application is written in php, bash and vbscript. These are all 'scripting' languages - no compiling and human readable source code. Making changes and customisations is both quick and easy.

Windows PCs can be queried for hardware, software, operating system settings, security settings, IIS settings, services, users & groups and much more. Linux systems can be queried for a similar amount of information. Network devices (printers, switches, routers, etc) can have data recorded such as IP-Address, MAC Address, open ports, serial number, etc, etc.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

Nmap is a prerequisite for Open-AudIT. We rely on Nmap for discovering devices on your network. The Linux installer will install this and the other prerequisites. The Windows installer needs the user to manually install Nmap.

The other Linux prerequisites are:

#### RedHat / Centos

awscli curl httpd mariadb-server nmap php php-cli php-ldap php-mbstring php-mcrypt php-mysql php-process php-snmp php-xml samba-client screen sshpass wget zip

#### Debian / Ubuntu

apache2 apache2-utils awscli curl libapache2-mod-php mariadb-server nmap openssh-client php php-cli php-curl php-ldap php-mbstring php-mysql php-snmp php-xml screen smbclient sshpass wget zip

### Installing

Installing is simply a matter of downloading the .run or .exe (Linux or Windows). The installer is self-contained and will install Linux prerequisites. Windows users will need to download and install Nmap.

To install from this source, please it in /usr/local/open-audit and copy the www folder into your web root under /open-audit. Initialise a MySQL database using the credentials found in code_igniter/application/config/database.php. The MySQL schema can be found in other/open-audit_mysql.sql.

## Built With

* [CodeIgniter](http://www.codeigniter.com/) - The web framework used
* [JsonAPI](http://jsonapi.org/) - API specification
* [jQuery](https://jquery.com/) - Javascript
* [Bootstrap](http://getbootstrap.com/) - Web Frontend

## Contributing

Please contact [Opmantek](https://opmantek.com) with any enquiries for contributing or post to the [Questions](https://community.opmantek.com/questions) site.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/Opmantek/open-audit/releases). 

## Authors

* **Mark Unwin** - [Mark Unwin](https://github.com/mark-unwin)

## License

This project is licensed under the aGPL License - see the [AGPL 3.0](agpl-3.0.txt) file for details

## Acknowledgments

* Thanks for all the forum contributions.
* Thanks in particular to JPA (forum user) for his countless contributions and testing work.
