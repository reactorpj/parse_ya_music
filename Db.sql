create database yandex_music;

use yandex_music;

create table if not exists artists (
                         id int unsigned primary key auto_increment,
                         title varchar(255) character set utf8mb4 collate utf8mb4_0900_ai_ci,
                         outer_id varchar(255) character set latin1 collate latin1_general_ci,
                         favorite_count int,
                         audition_count int,
                         album_count int,
                         created_at timestamp default current_timestamp,
                         updated_at timestamp default current_timestamp on update current_timestamp
) ENGINE = InnoDb;

create table if not exists tracks (
                        id int unsigned primary key auto_increment,
                        title varchar(255) character set utf8mb4 collate utf8mb4_0900_ai_ci,
                        outer_id varchar(255) character set latin1 collate latin1_general_ci,
                        duration varchar(10) character set latin1 collate latin1_general_ci,
                        artist_id int unsigned,
                        foreign key (artist_id) references artists(id)
) ENGINE = InnoDb;