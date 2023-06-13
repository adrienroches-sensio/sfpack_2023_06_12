#!/usr/bin/env bash

symfony console doctrine:query:sql \
  "INSERT INTO movie (title, slug, plot, poster, released_at) VALUES ('Astérix et Obélix: Mission Cléopâtre', '2002-asterix-et-obelix-mission-cleopatre', 'plot', '2002-asterix-et-obelix-mission-cleopatre.png', '2002-01-30 00:00:00')" \
;
symfony console doctrine:query:sql \
  "INSERT INTO movie (title, slug, plot, poster, released_at) VALUES ('Le sens de la fête', '2017-le-sens-de-la-fete', 'plot', '2017-le-sens-de-la-fete.png', '2017-10-04 00:00:00')" \
;
symfony console doctrine:query:sql \
  "INSERT INTO movie (title, slug, plot, poster, released_at) VALUES ('Avatar', '2017-avatar', 'plot', '2009-avatar.png', '2009-12-16 00:00:00')" \
;

symfony console doctrine:query:sql "INSERT INTO Genre (name) VALUES ('Comedy'), ('Famille')"

symfony console doctrine:query:sql "INSERT INTO movie_genre (movie_id, genre_id) VALUES (1, 1)"
symfony console doctrine:query:sql "INSERT INTO movie_genre (movie_id, genre_id) VALUES (2, 2)"