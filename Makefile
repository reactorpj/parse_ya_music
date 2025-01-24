up:
	@docker compose up -d --build
	@docker exec yamusphp composer install

parse:
	@docker exec yamusphp php Example.php

showArtists:
	@docker exec yamusmysql mysql -u root -proot -D yandex_music -e "select * from artists limit 5" --table

showTracks:
	@docker exec yamusmysql mysql -u root -proot -D yandex_music -e "select * from tracks limit 5" --table

artists:
	@docker exec yamusmysql mysql -u root -proot -D yandex_music -e "select * from artists" --table

tracks:
	@docker exec yamusmysql mysql -u root -proot -D yandex_music -e "select * from tracks" --table

execute:
	@up @parse @showArtists @showTracks

down:
	@docker compose down
	@docker volume rm yamusdb
	@echo "Thank you and see you in future ;)"

