CREATE PROCEDURE add_foreign_keys()
BEGIN
  -- ARTICLE
  ALTER TABLE `article`
    ADD KEY `fk_article_thing_idx` (`thing`),
    ADD KEY `fk_article_creativeWork_idx` (`creativeWork`),
    ADD CONSTRAINT `fk_article_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_article_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- BOOK
  ALTER TABLE `book`
    ADD KEY `fk_book_thing_idx` (`thing`),
    ADD KEY `fk_book_creativeWork_idx` (`creativeWork`),
    ADD CONSTRAINT `fk_book_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_book_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- CONTACT POINT
  ALTER TABLE `contactPoint`
    ADD KEY `fk_contactPoint_thing_idx` (`thing`),
    ADD CONSTRAINT `fk_contactPoint_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- CREATIVE WORK
  ALTER TABLE `creativeWork`
    ADD KEY `fk_creativeWork_thing_idx` (`thing`),
    ADD CONSTRAINT `fk_creativeWork_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- EVENT
  ALTER TABLE `event`
    ADD KEY `fk_event_thing_idx` (`thing`),
    ADD KEY `fk_event_subEvent_idx` (`subEvent`),
    ADD KEY `fk_event_superEvent_idx` (`superEvent`),
    ADD CONSTRAINT `fk_event_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_event_subEvent` FOREIGN KEY (`subEvent`) REFERENCES `event` (`idevent`) ON DELETE SET NULL ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_event_superEvent` FOREIGN KEY (`superEvent`) REFERENCES `event` (`idevent`) ON DELETE SET NULL ON UPDATE NO ACTION;
  -- GEO COORDINATES
  ALTER TABLE `geoCoordinates`
    ADD KEY `fk_geoCoordinates_postalAddress_idx` (`address`),
    ADD CONSTRAINT `fk_geoCoordinates_postalAddress` FOREIGN KEY (`address`) REFERENCES `postalAddress` (`idpostalAddress`) ON DELETE SET NULL ON UPDATE NO ACTION;
  -- IMAGE OBJECT
  ALTER TABLE `imageObject`
    ADD KEY `fk_imageObject_thing_idx` (`thing`),
    ADD KEY `fk_imageObject_creativeWork_idx` (`creativeWork`),
    ADD KEY `fk_imageObject_mediaObject_idx` (`mediaObject`),
    ADD CONSTRAINT `fk_imageObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_imageObject_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_imageObject_mediaObject` FOREIGN KEY (`mediaObject`) REFERENCES `mediaObject` (`idmediaObject`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- INVOICE
  ALTER TABLE `invoice`
    ADD KEY `fk_invoice_order_idx` (`referencesOrder`),
    ADD CONSTRAINT `fk_invoice_order` FOREIGN KEY (`referencesOrder`) REFERENCES `order` (`idorder`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- LOCAL BUSINESS
  ALTER TABLE `localBusiness`
    ADD KEY `fk_localBusiness_thing_idx` (`thing`),
    ADD KEY `fk_localBusiness_organization_idx` (`organization`),
    ADD KEY `fk_localBusiness_place_idx` (`location`),
    ADD CONSTRAINT `fk_localBusiness_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_localBusiness_organization` FOREIGN KEY (`organization`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_localBusiness_place` FOREIGN KEY (`location`) REFERENCES `place` (`idplace`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- MEDIA OBJECT
  ALTER TABLE `mediaObject`
    ADD KEY `fk_mediaObject_thing_idx` (`thing`),
    ADD KEY `fk_mediaObject_creativeWork_idx` (`creativeWork`),
    ADD CONSTRAINT `fk_mediaObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_mediaObject_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- OFFER
  ALTER TABLE `offer`
    ADD KEY `fk_offer_thing_idx` (`thing`),
    ADD KEY `fk_offer_itemOffered_thing_idx` (`itemOffered`),
    ADD KEY `fk_offer_offeredBy_thing_idx` (`offeredBy`),
    ADD CONSTRAINT `fk_offer_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_offer_itemOffered_thing` FOREIGN KEY (`itemOffered`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_offer_offeredBy_thing` FOREIGN KEY (`offeredBy`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- ORDER
  ALTER TABLE `order`
    ADD KEY `fk_order_customer_idx` (`customer`),
    ADD KEY `fk_order_seller_idx` (`seller`),
    ADD CONSTRAINT `fk_order_customer` FOREIGN KEY (`customer`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_order_seller` FOREIGN KEY (`seller`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- ORDER ITEM
  ALTER TABLE `orderItem`
    ADD KEY `fk_orderItem_offer_idx` (`offer`),
    ADD KEY `fk_orderedItem_thing_idx` (`orderedItem`),
    ADD KEY `fk_orderItemNumber_thing_idx` (`orderItemNumber`),
    ADD CONSTRAINT `fk_orderedItem_offer` FOREIGN KEY (`offer`) REFERENCES `offer` (`idoffer`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_orderedItem_thing` FOREIGN KEY (`orderedItem`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_orderItemNumber_thing` FOREIGN KEY (`orderItemNumber`) REFERENCES `order` (`idorder`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- ORGANIZATION
  ALTER TABLE `organization`
    ADD KEY `fk_organization_thing_idx` (`thing`),
    ADD KEY `fk_organization_location_idx` (`location`),
    ADD CONSTRAINT `fk_organization_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_organization_location` FOREIGN KEY (`location`) REFERENCES `place` (`idplace`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- PERSON
  ALTER TABLE `person`
    ADD KEY `fk_person_thing_idx` (`thing`),
    ADD CONSTRAINT `fk_person_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- PLACE
  ALTER TABLE `place`
    ADD KEY `fk_place_thing_idx` (`thing`),
    ADD KEY `fk_place_geo_idx` (`geo`),
    ADD CONSTRAINT `fk_place_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_place_geo` FOREIGN KEY (`geo`) REFERENCES `geoCoordinates` (`idgeoCoordinates`) ON DELETE SET NULL ON UPDATE NO ACTION;
  -- POSTAL ADDRESS
  -- PRODUCT
  ALTER TABLE `product`
    ADD KEY `fk_product_thing_idx` (`thing`),
    ADD KEY `fk_product_manufacturer_idx` (`manufacturer`),
    ADD CONSTRAINT `fk_product_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_product_manufacturer` FOREIGN KEY (`manufacturer`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- REVIEW
  ALTER TABLE `review`
    ADD KEY `fk_review_thing_idx` (`thing`),
    ADD KEY `fk_review_itemReviewed_idx` (`itemReviewed`),
    ADD CONSTRAINT `fk_review_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_review_itemReviewed` FOREIGN KEY (`itemReviewed`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- SERVICE
  ALTER TABLE `service`
    ADD KEY `fk_service_thing_idx` (`thing`),
    ADD KEY `fk_service_provider_idx` (`provider`),
    ADD CONSTRAINT `fk_service_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_service_provider` FOREIGN KEY (`provider`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- TAXON
  ALTER TABLE `taxon`
    ADD KEY `fk_taxon_thing_idx` (`thing`),
    ADD CONSTRAINT `fk_taxon_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- THING HAS IMAGE OBJECT
  ALTER TABLE `thing_has_imageObject`
    ADD KEY `fk_thing_has_imageObject_idthing_idx` (`idthing`),
    ADD KEY `fk_thing_has_imageObject_idimageObject_idx` (`idimageObject`),
    ADD CONSTRAINT `fk_thing_has_imageObject_idthing` FOREIGN KEY (`idthing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_thing_has_imageObject_idimageObject` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- THING HAS THING
  ALTER TABLE `thing_has_thing`
    ADD KEY `fk_thing_has_thing_idHasPart_idx` (`idHasPart`),
    ADD KEY `fk_thing_has_thing_idIsPartOf_idx` (`idIsPartOf`),
    ADD CONSTRAINT `fk_thing_has_thing_idHasPart` FOREIGN KEY (`idHasPart`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_thing_has_thing_idIsPartOf` FOREIGN KEY (`idIsPartOf`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- VIDEO OBJECT
  ALTER TABLE `videoObject`
    ADD KEY `fk_videoObject_thing_idx` (`thing`),
    ADD KEY `fk_videoObject_creativeWork_idx` (`creativeWork`),
    ADD KEY `fk_videoObject_mediaObject_idx` (`mediaObject`),
    ADD CONSTRAINT `fk_videoObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_videoObject_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_videoObject_mediaObject` FOREIGN KEY (`mediaObject`) REFERENCES `mediaObject` (`idmediaObject`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- WEB PAGE
  ALTER TABLE `webPage`
    ADD KEY `fk_webPage_thing_idx` (`thing`),
    ADD KEY `fk_webPage_creativeWork_idx` (`creativeWork`),
    ADD CONSTRAINT `fk_webPage_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_webPage_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- WEB PAGE ELEMENT
  ALTER TABLE `webPageElement`
    ADD KEY `fk_webPageElement_thing_idx` (`thing`),
    ADD KEY `fk_webPageElement_creativeWork_idx` (`creativeWork`),
    ADD CONSTRAINT `fk_webPageElement_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_webPageElement_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;
  -- WEB SITE
  ALTER TABLE `webSite`
    ADD KEY `fk_webSite_thing_idx` (`thing`),
    ADD KEY `fk_webSite_creativeWork_idx` (`creativeWork`),
    ADD CONSTRAINT `fk_webSite_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_webSite_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;
END;