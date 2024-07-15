-- THING

-- MEDIA OBJECT
ALTER TABLE `mediaObject`
  CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idmediaObject`,`creativeWork`),
  ADD KEY `fk_mediaObject_creativeWork_idx` (`creativeWork`),
  ADD CONSTRAINT `fk_mediaObject_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE;

-- ORGANIZATION
ALTER TABLE `organization`
  DROP COLUMN `dateModified`,
  DROP COLUMN `dateCreated`;
/*
ALTER TABLE `article_has_imageObject`
  ADD CONSTRAINT `fk_article_has_imageObject_imageObject` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE RESTRICT;

ALTER TABLE `book_has_imageObject`
  ADD CONSTRAINT `fk_book_has_imageObject_imageObject` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE RESTRICT;

ALTER TABLE `event_has_imageObject`
  ADD CONSTRAINT `fk_event_has_imageObject_imageObject` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE RESTRICT;

ALTER TABLE `galleries_images`
  ADD CONSTRAINT `fk_galleries_images_imageObject` FOREIGN KEY (`idimages`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE RESTRICT;
*/
-- -- --