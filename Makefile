up:
	@docker compose up -d --build
	@docker exec yamusphp composer install
	@sleep 10

parse:
	@docker exec yamusphp php Example.php

showArtists:
	@docker exec yamusmysql mysql -u root -proot -D yandex_music -e "select * from artists limit 5" --table

showTracks:
	@docker exec yamusmysql mysql -u root -proot -D yandex_music -e "select * from tracks limit 5" --table

execute:
	@up @parse @showArtists @showTracks

down:
	@docker compose down
	@docker volume rm yamusdb
	@echo "Thank you and see you in future ;)"

