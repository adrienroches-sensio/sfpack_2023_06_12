#!/usr/bin/env bash

symfony console doctrine:query:sql \
  "INSERT INTO movie (title, slug, plot, rated, poster, released_at) VALUES ('Astérix et Obélix: Mission Cléopâtre', '2002-asterix-et-obelix-mission-cleopatre', 'plot', 'NC-17', '2002-asterix-et-obelix-mission-cleopatre.png', '2002-01-30 00:00:00')" \
;
symfony console doctrine:query:sql \
  "INSERT INTO movie (title, slug, plot, rated, poster, released_at) VALUES ('Le sens de la fête', '2017-le-sens-de-la-fete', 'plot', 'PG-13', '2017-le-sens-de-la-fete.png', '2017-10-04 00:00:00')" \
;
symfony console doctrine:query:sql \
  "INSERT INTO movie (title, slug, plot, rated, poster, released_at) VALUES ('Avatar', '2017-avatar', 'plot', 'G', '2009-avatar.png', '2009-12-16 00:00:00')" \
;

symfony console doctrine:query:sql "INSERT INTO Genre (name) VALUES ('Comedy'), ('Famille')"

symfony console doctrine:query:sql "INSERT INTO movie_genre (movie_id, genre_id) VALUES (1, 1)"
symfony console doctrine:query:sql "INSERT INTO movie_genre (movie_id, genre_id) VALUES (2, 2)"

symfony console doctrine:query:sql \
  "INSERT INTO user (username, roles, password, birthdate) VALUES ('adrien', '[\"ROLE_ADMIN\"]', '\$2y\$13\$7iLRI.zz6foUch5qmvms8eqmlG07seWC6aLwZ/8pvL7WPltk8jPDu', '1945-03-24 00:00:00')" \
;
symfony console doctrine:query:sql \
  "INSERT INTO user (username, roles, password, birthdate) VALUES ('max', '[]', '\$2y\$13\$1o5q7EherDhCnVPQJR4/I.HHmBZuHZ7C2.BLSZIi1kQMLEQPDITLi', '2008-06-10 00:00:00')" \
;
