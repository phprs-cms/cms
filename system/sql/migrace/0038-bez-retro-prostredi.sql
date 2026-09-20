-- Administrace má jediný vzhled: volba prostředí (retro / 2026) končí.
ALTER TABLE rs_user DROP COLUMN prostredi;
DELETE FROM rs_config WHERE promenna = 'prostredi_admin';
