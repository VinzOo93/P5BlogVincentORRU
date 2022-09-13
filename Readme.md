
![image](https://user-images.githubusercontent.com/60787347/188748155-10646e21-a545-4bc1-a2bd-7bda5491ad06.png)

Prérequis : 

Pour que les Url fonctionnent correctement,
Installer un Vhost qui pointe dans le dossier "P5BlogVincentORRU/public" du repo 
comme indiqué dans le tuto via ce lien  :
https://blog.gary-houbre.fr/developpement/mamp-comment-bien-installer-notre-projet-symfony-sur-mac

exemple de config du fichier /etc/apache2/extra/httpd-vhosts.conf : 


<VirtualHost *:80>

DocumentRoot "/Users/vincentorru/VHost/P5BlogVincentORRU/public"

ServerName blog.local

<Directory "/Users/vincentorru/VHost/P5BlogVincentORRU/public">


Installation : 

Etape 1  : Se rendre dans le dossier à l'intérieur du repertoire de votre server local :

Etape 2  : cd "votre chemin devotre repo" lancer la commande : git clone https://github.com/VinzOo93/P5BlogVincentORRU.git

Etape 3 : pour installer les librairies nécessaires avec composer : cd P5BlogVincentORRU => composer install

Etape 4 : Pour créer la base de données, vous devez d'abord configurer correctement le fichier config.ini avec vos identifiant de connexion de votre base de données
Lancer les scripts MySql dans l'ordre dans le dossier script pour créer les tables.

une fois ces étapes éffectuées vous pouvez accéder à l'index du blog  :

http://votreVhost/blog/

Bonne visite
