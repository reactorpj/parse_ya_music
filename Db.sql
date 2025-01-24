create database if not exists yandex_music;

use yandex_music;

create table if not exists artists (
                        id int unsigned primary key auto_increment,
                        title varchar(255) character set utf8mb4 collate utf8mb4_0900_as_cs,
                        outer_id varchar(255) character set utf8mb4 collate utf8mb4_0900_ai_ci,
                        favorite_count int unsigned,
                        audition_count int unsigned,
                        album_count int unsigned,
                        created_at timestamp default current_timestamp,
                        updated_at timestamp default current_timestamp on update current_timestamp,
                        unique index idx_artist_outer_id (outer_id)
) ENGINE = InnoDb;

create table if not exists tracks (
                        id int unsigned primary key auto_increment,
                        title varchar(255) character set utf8mb4 collate utf8mb4_0900_as_cs,
                        outer_id varchar(255) character set utf8mb4 collate utf8mb4_0900_ai_ci,
                        duration int unsigned,
                        artist_id int unsigned,
                        created_at timestamp default current_timestamp,
                        updated_at timestamp default current_timestamp on update current_timestamp,
                        foreign key (artist_id) references artists(id),
                        unique index idx_track_outer_id (outer_id)
) ENGINE = InnoDb;