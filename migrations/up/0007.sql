ALTER TABLE `reading`
  ADD COLUMN `user_alias_id` bigint unsigned NOT NULL,
  ADD CONSTRAINT `fk_user_alias_id` FOREIGN KEY (`user_alias_id`) REFERENCES `user_alias`(`id`) ON DELETE CASCADE;
