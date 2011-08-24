DROP TABLE IF EXISTS "author";
CREATE TABLE "author" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "name" VARCHAR NOT NULL , "email" VARCHAR NOT NULL  UNIQUE );
INSERT INTO "author" VALUES(1,'Jac Wright','jacwright@gmail.com');
INSERT INTO "author" VALUES(2,'Arul Kumaran','arul@luracast.com');
