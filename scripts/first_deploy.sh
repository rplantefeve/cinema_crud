#!/bin/bash

# Function to display usage instructions
print_usage() {
    echo "Usage: $0 <project_directory> [--fpm <socket_path>]"
    echo "  <project_directory> : The directory where the project is located."
    echo "  --fpm <socket_path> : (Optional) Enable PHP-FPM with the specified socket path."
}

# Vérification du nombre d'arguments
if [ "$#" -lt 1 ] || [ "$#" -gt 3 ]; then
    echo "Error: Invalid number of arguments."
    print_usage
    exit 1
fi

fpm_flag=false
fpm_socket=""
project_dir=""

# Analyse des arguments
while [ $# -gt 0 ]; do
    key="$1"
    case $key in
        --fpm)
            fpm_flag=true
            shift
            if [ $# -eq 0 ] ; then
                echo "Error: --fpm option requires a socket path."
                print_usage
                exit 1
            fi
            fpm_socket="$1"
            ;;
        *)
            if [ -z "$project_dir" ]; then
                project_dir="$1"
            else
                echo "Error: Unexpected argument '$1'."
                print_usage
                exit 1
            fi
            ;;
    esac
    shift
done

# Check if the script is executed with a directory parameter
if [ -z "$project_dir" ]; then
    echo "Error: No project directory provided."
    print_usage
    exit 1
fi

# Check if the provided directory exists
if [ ! -d "$project_dir" ]; then
    echo "Error: The provided directory does not exist."
    print_usage
    exit 1
fi

# Définir le répertoire des scripts SQL
sql_dir="$project_dir/../db"

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
# add DocumentRoot dynamically with $project_dir
sudo sed -i "s|/var/www/html|${project_dir}|g" /etc/apache2/sites-available/cinema.local.conf
# add server alias
sudo sed -i '/DocumentRoot/i \\tServerAlias www.cinema.local' /etc/apache2/sites-available/cinema.local.conf
# add server name
sudo sed -i '/ServerAlias/i \\tServerName cinema.local' /etc/apache2/sites-available/cinema.local.conf
# insert Directory node
sudo sed -i "/<\/VirtualHost>/i \\\t<Directory ${project_dir}>\n\t\tOptions Indexes FollowSymLinks\n\t\tAllowOverride All\n\t\tRequire all granted\n\t<\/Directory>" /etc/apache2/sites-available/cinema.local.conf 2>&1 | sudo tee -a "$log_file"

# Add <FilesMatch> block if --fpm is provided
if [ -n "$fpm_socket" ]; then
    sudo sed -i "/<\/VirtualHost>/i \\\t<FilesMatch \\.php$>\n\t\tSetHandler \"proxy:unix:${fpm_socket}|fcgi://localhost/\"\n\t<\/FilesMatch>" /etc/apache2/sites-available/cinema.local.conf 2>&1 | sudo tee -a "$log_file"
    echo "FPM configuration added with socket path: $fpm_socket" | sudo tee -a "$log_file"
fi

echo "Apache site configuration for cinema.local created successfully."

# 2. Create a new database
sudo echo "DROP DATABASE IF EXISTS cinema_crud;" | sudo mysql 2>&1 | sudo tee -a "$log_file"
if ! sudo cat "$sql_dir"/00_create_base.sql > /dev/null ; then
    echo "Error: Failed to execute 00_create_base.sql. Aborting deployment." | sudo tee -a "$log_file" >&2
    exit 1
else
    sudo cat "$sql_dir"/00_create_base.sql | sudo mysql 2>&1 | sudo tee -a "$log_file"
fi
if ! sudo cat "$sql_dir"/01_insert_data.sql > /dev/null ; then
    echo "Error: Failed to execute 01_insert_data.sql. Aborting deployment." | sudo tee -a "$log_file" >&2
    exit 1
else
    sudo cat "$sql_dir"/01_insert_data.sql | sudo mysql 2>&1 | sudo tee -a "$log_file" 
fi
if ! sudo cat "$sql_dir"/02_create_constraints.sql > /dev/null ; then
    echo "Error: Failed to execute 02_create_constraints.sql. Aborting deployment." | sudo tee -a "$log_file" >&2
    exit 1
else
    sudo cat "$sql_dir"/02_create_constraints.sql | sudo mysql 2>&1 | sudo tee -a "$log_file"
fi
echo "Database cinema_crud created and initialized successfully."

# 3. Create a new user and grant privileges to the database
sudo echo "DROP USER IF EXISTS 'cinema'@'localhost';" | sudo mysql 2>&1 | sudo tee -a "$log_file"
if ! sudo cat "$sql_dir"/03_create_user.sql > /dev/null ; then
    echo "Error: Failed to execute 03_create_user.sql. Aborting deployment." | sudo tee -a "$log_file" >&2
    exit 1
else
    sudo cat "$sql_dir"/03_create_user.sql | sudo mysql 2>&1 | sudo tee -a "$log_file"
fi
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

# 7. Dealing with permissions
sudo chown -R www-data:www-data "$project_dir" 2>&1 | sudo tee -a "$log_file" > /dev/null
sudo chmod -R 755 "$project_dir" 2>&1 | sudo tee -a "$log_file" > /dev/null
echo "Permissions set successfully."

echo "Deployment completed at $(date)"
