DROP TABLE IF EXISTS `civicrm_favrikmembershipperiod`;

CREATE TABLE `civicrm_favrikmembershipperiod` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique FavrikMembershipPeriod ID',
     `contact_id` int unsigned    COMMENT 'FK to Contact',
     `membership_id` int unsigned    COMMENT 'FK to Membership',
     `contribution_id` int unsigned    COMMENT 'FK to Contribution',
     `start_date` date    COMMENT 'Period Start Date',
     `end_date` date    COMMENT 'Period End Date'
,
        PRIMARY KEY (`id`)


,          CONSTRAINT FK_civicrm_favrikmembershipperiod_contact_id FOREIGN KEY (`contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_favrikmembershipperiod_membership_id FOREIGN KEY (`membership_id`) REFERENCES `civicrm_membership`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_favrikmembershipperiod_contribution_id FOREIGN KEY (`contribution_id`) REFERENCES `civicrm_contribution`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

