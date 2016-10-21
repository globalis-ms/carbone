PHP=/usr/local/bin/php
CLOC=/usr/local/bin/cloc
ECHO=/bin/echo
ZIP=/usr/bin/zip
DIRNAME=$(notdir $(shell pwd))
BASENAME=$(basename $(shell pwd))
OS := $(shell uname)

os:
	@ $(ECHO) -n "Operating System : "
	@ $(ECHO) $(OS)

cloc:
	@ $(ECHO) "Statistiques"
	@ $(CLOC) --exclude-dir=divers ./

reset_all: reset_user reset_data reset_session

reset_user:
	@ $(ECHO) -n "Reset de la table 'user' : "
	@ cd divers/outils/; $(PHP) database.php -u
	@ $(ECHO) "OK"

reset_data:
	@ $(ECHO) -n "Reset de la table 'data' : "
	@ cd divers/outils/; $(PHP) database.php -d
	@ $(ECHO) "OK"

reset_session:
	@ $(ECHO) -n "Reset de la table 'session' : "
	@ cd divers/outils/; $(PHP) database.php -s
	@ $(ECHO) "OK"

check_str:
	@ echo "Check des chaines"
	@ cd divers/outils/; $(PHP) langue.php -s

check_dir:
	@ $(ECHO) -n "Check des repertoires : "
	@ chmod 777 web/upload web/upload/foo
	@ chmod 777 web/cache
	@ $(ECHO) "OK"

clean_all: clean_cache clean_file

clean_cache:
	@ $(ECHO) -n "Suppression des fichiers cache : "
	@ if [ -d "web/cache" ]; then cd "web/cache"; rm -f *; fi
	@ $(ECHO) "OK"

clean_file:
	@ $(ECHO) -n "Suppression des fichiers inutiles : "
	@ rm -f $(DIRNAME).zip
ifeq ($(OS), Darwin)
	@ find ./ -type f | grep .DS_Store | xargs rm
	@ find . -iname "._*" | xargs rm
	@ find . -iname "*~" | xargs rm
	@ find ./ -type f | grep Thumbs.db | xargs rm
else
	@ find ./ -type f | grep .DS_Store | xargs -r rm
	@ find . -iname "._*" | xargs -r rm
	@ find . -iname "*~" | xargs -r rm
	@ find ./ -type f | grep Thumbs.db | xargs -r rm
endif
	@ $(ECHO) "OK"

zip: clean_all
	@ $(ECHO) -n "Creation de l'archive Zip : "
	@ rm -f $(DIRNAME).zip
	@ cd ..; $(ZIP) -r /tmp/$(DIRNAME).zip $(DIRNAME) > /dev/null
	@ mv /tmp/$(DIRNAME).zip $(BASENAME)
	@ $(ECHO) "OK"
