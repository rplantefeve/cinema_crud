#!/bin/bash

# Check if the script is executed with a directory parameter
if [ -z "$1" ]; then
    echo "Error: No project directory provided."
    echo "Usage: $0 <project_directory>"
    exit 1
fi

# Check if the provided directory exists
if [ ! -d "$1" ]; then
    echo "Error: The provided directory does not exist."
    exit 1
fi

# récupérer le répertoire d'installation du projet passé en paramètre du script
project_dir=$1

# Définir le fichier de log
log_file="/var/log/cinema_crud_deploy.log"

# Vérifier les permissions d'écriture sur le fichier de log
if ! touch "$log_file" 2>/dev/null; then
    echo "Error: Cannot write to log file $log_file. Please check permissions." >&2
    exit 1
fi
echo "Deployment started at $(date)" > "$log_file"

# 1. Create a new available site in Apache
sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/cinema.local.conf | sudo tee -a "$log_file" > /dev/null 2>&1
# add DocumentRoot dynamically with $projet_dir
sudo sed -i "s|/var/www/html|${project_dir}|g" /etc/apache2/sites-available/cinema.local.conf
# add server alias
sudo sed -i '/DocumentRoot/i \\tServerAlias www.cinema.local' /etc/apache2/sites-available/cinema.local.conf
# add server name
sudo sed -i '/ServerAlias/i \\tServerName cinema.local' /etc/apache2/sites-available/cinema.local.conf
# insert Directory node
sudo sed -i "/<\/VirtualHost>/i \\\t<Directory ${project_dir}>\n\t\tOptions Indexes FollowSymLinks\n\t\tAllowOverride All\n\t\tRequire all granted\n\t<\/Directory>" /etc/apache2/sites-available/cinema.local.conf 2>&1 | sudo tee -a "$log_file"
echo "Apache site configuration for cinema.local created successfully."

# 2. Create a new database
sudo echo "DROP DATABASE IF EXISTS cinema_crud;" | sudo mysql 2>&1 | sudo tee -a "$log_file"
sudo cat "$project_dir"/db/00_create_base.sql | sudo mysql 2>&1 | sudo tee -a "$log_file"
sudo cat "$project_dir"/db/01_insert_data.sql | sudo mysql 2>&1 | sudo tee -a "$log_file" 
sudo cat "$project_dir"/db/02_create_constraints.sql | sudo mysql 2>&1 | sudo tee -a "$log_file"
echo "Database cinema_crud created and initialized successfully."

# 3. Create a new user and grant privileges to the database
sudo echo "DROP USER IF EXISTS 'cinema'@'localhost';" | sudo mysql 2>&1 | sudo tee -a "$log_file"
sudo cat "$project_dir"/db/03_create_user.sql | sudo mysql 2>&1 | sudo tee -a "$log_file"
echo "Database user created and privileges granted successfully."

# 4. Add a new entry in the hosts file
if ! grep -q '127.0.0.1    cinema.local' /etc/hosts; then
    echo '127.0.0.1    cinema.local' | sudo tee -a /etc/hosts
    echo "Hosts file updated successfully."
else
    echo "Hosts file already contains the entry for cinema.local."
fi >> "$log_file" 2>&1

# 5. Enable the new site in Apache
if sudo a2query -s cinema.local | grep -q 'enabled'; then
    sudo a2dissite cinema.local 2>&1 | sudo tee -a "$log_file" > /dev/null
fi
sudo a2ensite cinema.local 2>&1 | sudo tee -a "$log_file" > /dev/null
echo "Apache site cinema.local enabled successfully."

# 6. Restart Apache
sudo systemctl restart apache2 2>&1 | sudo tee -a "$log_file" > /dev/null
echo "Apache server restarted successfully."

echo "Deployment completed at $(date)"

