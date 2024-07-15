ALTER TABLE `article`
  DROP INDEX `idx_1` ,
  DROP INDEX `id` ,
  DROP INDEX `autor`;

ALTER TABLE `article_has_imageObject`
  DROP INDEX `FK_news_has_images_images`,
  DROP FOREIGN KEY `fk_article_has_imageObject_1`,
  DROP FOREIGN KEY `FK_news_has_images_news`,
  DROP PRIMARY KEY ;

ALTER TABLE `book`
  DROP INDEX `titulo`;

ALTER TABLE `book_has_imageObject`
  DROP INDEX `fk_book_has_imageObject_imageObject_idx`,
  DROP FOREIGN KEY `fk_book_has_imageObject_book`,
  DROP FOREIGN KEY `fk_book_has_imageObject_imageObject`,
  DROP PRIMARY KEY;

ALTER TABLE `event`
  DROP INDEX `idx_1`,
  DROP INDEX `idx_2`;

ALTER TABLE `event_has_event`
  DROP INDEX `fk_event_has_event_event2_idx`,
  DROP INDEX `fk_event_has_event_event1_idx`,
  DROP FOREIGN KEY `fk_event_has_event_event1`,
  DROP FOREIGN KEY `fk_event_has_event_event2`,
  DROP PRIMARY KEY;

ALTER TABLE `event_has_imageObject`
  DROP FOREIGN KEY `fk_event_has_imageObject_event1`,
  DROP FOREIGN KEY `FK_event_has_imageObject_imageObject1`,
  DROP INDEX `FK_events_has_images_imageObject`,
  DROP PRIMARY KEY;

ALTER TABLE `galleries_images`
  DROP FOREIGN KEY `FK_galleries_images_galleries`,
  DROP FOREIGN KEY `FK_galleries_images_images`;

ALTER TABLE `images_has_attributes`
  DROP FOREIGN KEY `fk_images_has_attributes_attributes1`,
  DROP FOREIGN KEY `fk_images_has_attributes_images1`,
  DROP INDEX `fk_images_has_attributes_attributes1_idx`,
  DROP INDEX `fk_images_has_attributes_images1_idx`,
  DROP PRIMARY KEY;

ALTER TABLE `invoice`
  DROP FOREIGN KEY `fk_invoice_order1`,
  DROP INDEX `idx_1`,
  DROP INDEX `idx_2`,
  DROP INDEX `idx_3`;

ALTER TABLE `localBusiness`
  DROP FOREIGN KEY `fk_localBusiness_1`;

ALTER TABLE `localBusiness_has_contactPoint`
  DROP FOREIGN KEY `fk_localBusiness_has_contactPoint_1`,
  DROP FOREIGN KEY `fk_localBusiness_has_contactPoint_2`;

ALTER TABLE `localBusiness_has_imageObject`
  DROP FOREIGN KEY `FK_produtos_has_images_images`,
  DROP FOREIGN KEY `FK_produtos_has_images_produtos`;

ALTER TABLE `localBusiness_has_person`
  DROP FOREIGN KEY `fk_localBusiness_has_person_localBusiness1`,
  DROP FOREIGN KEY `fk_localBusiness_has_person_person1`;

ALTER TABLE `order_has_history`
  DROP INDEX `fk_contratos_has_history_history1_idx`,
  DROP INDEX `fk_contratos_has_history_order1_idx`,
  DROP FOREIGN KEY `fk_advertising_has_history_1`,
  DROP FOREIGN KEY `fk_advertising_has_history_2`;

ALTER TABLE `orderItem`
  DROP FOREIGN KEY `fk_orderItem_order1`;

ALTER TABLE `organization_has_contactPoint`
  DROP FOREIGN KEY `fk_Organization_has_contactPoint_contactPoint1`,
  DROP FOREIGN KEY `fk_organization_has_contactPoints_organization1`;

ALTER TABLE `organization_has_imageObject`
  DROP FOREIGN KEY `fk_Organization_has_imageObject_1`,
  DROP FOREIGN KEY `fk_organization_has_imageObject_organization1`;

ALTER TABLE `organization_has_person`
  DROP FOREIGN KEY `fk_Organization_has_person_organization1`,
  DROP FOREIGN KEY `fk_Organization_has_person_person1`;

ALTER TABLE `person`
  DROP INDEX `fk_person_1_idx`,
  DROP FOREIGN KEY `fk_person_1`;

ALTER TABLE `person_has_contactPoint`
  DROP FOREIGN KEY `fk_person_has_contactPoint_contactPoint1`,
  DROP FOREIGN KEY `fk_person_has_contactPoint_person`;

ALTER TABLE `person_has_imageObject`
  DROP FOREIGN KEY `fk_person_has_imageObject_imageObject1`,
  DROP FOREIGN KEY `fk_person_has_imageObject_person1`;

ALTER TABLE `place`
  DROP FOREIGN KEY `fk_place_1`;

ALTER TABLE `place_has_imageObject`
  DROP FOREIGN KEY `fk_place_has_imageObject_imageObject1`,
  DROP FOREIGN KEY `fk_place_has_imageObject_place1`;

ALTER TABLE `product`
  DROP FOREIGN KEY `fk_product_organization1`;

ALTER TABLE `product_has_imageObject`
  DROP FOREIGN KEY `fk_product_has_imageObject_imageObject1`,
  DROP FOREIGN KEY `fk_product_has_imageObject_product1`;

ALTER TABLE `product_has_offer`
  DROP FOREIGN KEY `fk_product_has_offer_offer1`,
  DROP FOREIGN KEY `fk_product_has_offer_product1`;

ALTER TABLE `service`
  DROP FOREIGN KEY `fk_service_organization1`;

ALTER TABLE `service_has_imageObject`
  DROP FOREIGN KEY `fk_service_has_imageObject_imageObject1`,
  DROP FOREIGN KEY `fk_service_has_imageObject_service1`;

ALTER TABLE `service_has_offer`
  DROP FOREIGN KEY `fk_service_has_offer_offer1`,
  DROP FOREIGN KEY `fk_service_has_offer_service1`;

ALTER TABLE `taxon_has_imageObject`
  DROP FOREIGN KEY `FK_taxon_has_imageObject`,
  DROP FOREIGN KEY `FK_taxon_has_imageObject_imageObject`;

ALTER TABLE `webPage_has_attributes`
  DROP FOREIGN KEY `fk_webPage_has_attributes_1`,
  DROP FOREIGN KEY `fk_webPage_has_attributes_2`;

ALTER TABLE `webPage_has_propertyValue`
  DROP FOREIGN KEY `fk_webPage_has_propertyValue_propertyValue2`,
  DROP FOREIGN KEY `fk_webPage_has_propertyValue_webPage1`;

ALTER TABLE `webPageElement_has_imageObject`
  DROP FOREIGN KEY `fk_webPageElement_has_images_1`,
  DROP FOREIGN KEY `fk_webPageElement_has_images_2`;

ALTER TABLE `webPageElement_has_propertyValue`
  DROP FOREIGN KEY `fk_webPageElement_has_attributes_1`,
  DROP FOREIGN KEY `fk_webPageElement_has_propertyValue_2`;

ALTER TABLE `webSite`
  DROP FOREIGN KEY `fk_webSite_1`,
  DROP FOREIGN KEY `fk_webSite_2`,
  DROP FOREIGN KEY `fk_webSite_3`;

ALTER TABLE `webSite_has_person`
  DROP FOREIGN KEY  `fk_webSite_has_person_person1`,
  DROP FOREIGN KEY `fk_webSite_has_person_webSite1`;
