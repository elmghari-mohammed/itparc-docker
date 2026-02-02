-- Scripts d'insertion corrigés
USE itparck;

-- =============================
-- 1. INSERTION DES DONNÉES DE BASE
-- =============================

-- Services
INSERT INTO service (nom, description) VALUES
('Pas encore décidé', 'Service par défaut pour les éléments non assignés'),
('Direction Générale', 'Gestion stratégique et prise de décision'),
('Ressources Humaines', 'Gestion du personnel, recrutement et formation'),
('Comptabilité', 'Suivi financier, gestion des comptes et budgets'),
('Informatique', 'Support IT, maintenance des systèmes et réseaux'),
('Commercial', 'Ventes, prospection et gestion de clients'),
('Production', 'Fabrication et gestion des ateliers de production'),
('Maintenance', 'Entretien et réparation des équipements'),
('Sécurité', 'Surveillance et protection des locaux et du personnel');

-- Types de matériel - CORRIGÉ avec IDs cohérents
INSERT INTO type (nom, description, est_personnel) VALUES
('Pas encore décidé', 'Type par défaut pour les équipements non classés', FALSE),
('PC Portable', 'Ordinateur portable destiné au travail mobile', TRUE),
('PC Bureau', 'Ordinateur fixe utilisé dans les bureaux', TRUE),
('Imprimante Laser', 'Imprimante rapide avec technologie laser', FALSE),
('Imprimante Jet d''encre', 'Imprimante polyvalente à jet d''encre', FALSE),
('Switch 8 ports', 'Switch réseau avec 8 ports Ethernet', FALSE),
('Switch 24 ports', 'Switch réseau avec 24 ports Ethernet', FALSE),
('Routeur', 'Équipement réseau pour diriger le trafic internet', FALSE),
('Serveur', 'Machine dédiée pour héberger des services ou applications', FALSE),
('Projecteur', 'Appareil de projection pour présentations et réunions', FALSE);

-- Salles
INSERT INTO salle (numero, nom, capacite, service_id) VALUES
('101', 'Bureau Direction', 3, 2),
('201', 'Bureau RH', 4, 3),
('202', 'Salle Réunion RH', 15, 3),
('301', 'Comptabilité Principal', 6, 4),
('401', 'Bureau Support IT', 5, 5),
('402', 'Salle Serveurs', 2, 5),
('501', 'Bureau Commercial', 4, 6),
('601', 'Atelier Production', 20, 7),
('701', 'Bureau Maintenance', 5, 8);

-- Service maintenance externe
INSERT INTO s_maintenance (id, numero, email, nom) VALUES
('SM_EXTERN_01', '0536-999-888', 'contact@maintenance-extern.ma', 'Maintenance Externe Pro'),
('SM_EXTERN_02', '0536-777-666', 'support@techservice.ma', 'TechService Solutions');

-- =============================
-- 2. INSERTION DES UTILISATEURS
-- =============================

-- Admin
INSERT INTO admin (matricul, nom, prenom, email, password, numero, N_T, service_id, salle_id, date_de_debut) VALUES
('ADM001', 'Admin', 'Principal', 'admin@gmail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '0536-000-111', 2, 2, 1, '2020-01-01'),
('ADM002', 'Ouali', 'Zineb', 'zouali@company.ma', '$2y$10$hashadm2', '0536-222-333', 1, 5, 6, '2021-05-10');

-- Agents
INSERT INTO agent (matricul, nom, prenom, tache, date_de_debut, post, email, password, N_T, service_id, salle_id) VALUES
('AGT001', 'Agent', 'Principal', 'Agent Standard', '2024-01-15', 'Général', 'agent@gmail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 3, 2, 1),
('AGT002', 'Benali', 'Fatima', 'Responsable RH', '2021-03-01', 'Gestion RH', 'fbenali@company.ma', '$2y$10$hash2', 2, 3, 2),
('AGT003', 'Chakir', 'Ahmed', 'Comptable', '2019-09-01', 'Comptabilité', 'achakir@company.ma', '$2y$10$hash3', 1, 4, 4),
('AGT004', 'Douiri', 'Nadia', 'Assistante RH', '2022-01-10', 'Assistance', 'ndouiri@company.ma', '$2y$10$hash4', 1, 3, 3),
('AGT005', 'El Fassi', 'Omar', 'Commercial Senior', '2020-06-15', 'Vente', 'oelfassi@company.ma', '$2y$10$hash5', 2, 6, 7),
('AGT006', 'Ghali', 'Aicha', 'Opératrice Production', '2021-11-01', 'Production', 'aghali@company.ma', '$2y$10$hash6', 1, 7, 8);

-- Supports
INSERT INTO support (matricul, nom, prenom, email, numero, password, N_T, service_id, salle_id, date_de_debut) VALUES
('SUP001', 'Support', 'Principal', 'support@gmail.com', '0536-777-888', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 4, 5, 6, '2023-03-05'),
('SUP002', 'Jamal', 'Rachid', 'rjamal@company.ma', '0536-333-444', '$2y$10$hashtech2', 3, 5, 5, '2021-02-15'),
('SUP003', 'Kabbaj', 'Salma', 'skabbaj@company.ma', '0536-555-666', '$2y$10$hashtech3', 2, 8, 9, '2020-09-20');

-- =============================
-- 3. INSERTION MATÉRIEL - CORRIGÉ AVEC LOGIQUE CLAIRE
-- =============================

-- A) MATÉRIELS PERSONNELS (est_personnel = TRUE) - attribués à des utilisateurs spécifiques
INSERT INTO materiel (serial_number, model, N_T, marque, garantie_fin, service_fin, detail, user_role, id_user, type_id) VALUES
('SN004LP2023', 'MacBook Pro', 1, 'Apple', '2026-12-01', NULL, 'PC portable premium pour direction', 'agent', 'AGT001', 2),  -- PC Portable (personnel)
('SN006DT2023', 'Vostro 3681', 1, 'Dell', '2024-02-15', NULL, 'PC bureau pour gestion RH', 'agent', 'AGT002', 3),        -- PC Bureau (personnel)
('SN008DT2023', 'OptiPlex 5070', 3, 'Dell', '2022-08-15', '2024-12-31', 'PC bureau comptabilité - fin de vie prévue', 'agent', 'AGT003', 3),  -- PC Bureau (personnel)
('SN001LP2024', 'ThinkPad X1', 0, 'Lenovo', '2027-01-15', NULL, 'PC portable haute performance pour direction', 'admin', 'ADM001', 2);  -- PC Portable (personnel)

-- B) MATÉRIELS PARTAGÉS (est_personnel = FALSE) - affectés à des salles/services spécifiques
INSERT INTO materiel (serial_number, model, N_T, marque, garantie_fin, service_fin, detail, service_id, salle_id, type_id) VALUES
('SN007PR2023', 'Color LaserJet', 2, 'HP', '2024-02-20', NULL, 'Imprimante couleur partagée - Bureau RH', 3, 2, 4),           -- Imprimante Laser (partagé - Salle RH)
('SN003PR2024', 'LaserJet Pro', 0, 'HP', '2026-01-20', NULL, 'Imprimante laser bureau commercial', 6, 7, 4),                   -- Imprimante Laser (partagé - Salle Commercial)
('SN020PROJ2024', 'PowerLite X49', 0, 'Epson', '2027-03-15', NULL, 'Projecteur salle réunion RH', 3, 3, 10),                   -- Projecteur (partagé - Salle Réunion RH)
('SN009SV2023', 'PowerEdge R750', 1, 'Dell', '2028-05-15', NULL, 'Serveur principal - Salle serveurs', 5, 6, 9),               -- Serveur (partagé - Salle Serveurs)
('SN010SW2023', 'Catalyst 2960', 0, 'Cisco', '2028-05-20', NULL, 'Switch réseau - Salle serveurs', 5, 6, 7),                   -- Switch 24 ports (partagé - Salle Serveurs)
('SN021PR2024', 'OfficeJet Pro', 0, 'HP', '2026-06-01', NULL, 'Imprimante bureau direction', 2, 1, 5);                         -- Imprimante Jet (partagé - Bureau Direction)

-- C) MATÉRIELS LIBRES/NON AFFECTÉS (disponibles pour attribution)
INSERT INTO materiel (serial_number, model, N_T, marque, garantie_fin, service_fin, detail, service_id, salle_id, type_id) VALUES
('SN002DT2024', 'OptiPlex 7090', 0, 'Dell', '2027-02-01', NULL, 'PC bureau standard - Stock central', NULL, NULL, 3),          -- PC Bureau (libre)
('SN012LP2024', 'ThinkPad T14', 0, 'Lenovo', '2027-03-01', NULL, 'PC portable - Stock informatique', NULL, NULL, 2),           -- PC Portable (libre)
('SN022SW2024', 'GS308', 0, 'Netgear', '2028-01-15', NULL, 'Switch 8 ports - Stock réseau', NULL, NULL, 6),                    -- Switch 8 ports (libre)
('SN011OLD2019', 'Vieux PC', 4, 'HP', '2022-01-01', '2024-01-01', 'PC hors service - À réformer', NULL, NULL, 1);              -- Pas encore décidé (libre)

-- =============================
-- 4. RÉCLAMATIONS - CORRIGÉ selon la philosophie
-- =============================

-- RÉCLAMATIONS POUR MATÉRIELS PERSONNELS (user spécifique)
INSERT INTO reclamation (id, date_reclamation, motif, type, statut, user_role, id_user, materiel_id) VALUES
('REC001', '2024-08-20', 'Problème démarrage Windows, écran bleu fréquent', 'software', 'en_attente', 'agent', 'AGT003', 3),  -- PC Ahmed (service Comptabilité)
('REC002', '2024-08-22', 'Microsoft Office ne s''ouvre plus, erreur activation', 'software', 'en_attente', 'agent', 'AGT002', 2);  -- PC Fatima (service RH)

-- RÉCLAMATIONS POUR MATÉRIELS PARTAGÉS (même service que l'équipement)
INSERT INTO reclamation (id, date_reclamation, motif, type, statut, support_matricul, motif_support, user_role, id_user, materiel_id) VALUES
('REC003', '2024-08-18', 'PC ne démarre plus du tout', 'hardware', 'en_cours', 'SUP001', 'Problème alimentation, pièce commandée', 'agent', 'AGT001', 1),  -- PC Agent Principal (service Direction)
('REC004', '2024-08-15', 'Imprimante ne sort plus de papier, bourrage constant', 'hardware', 'en_cours', 'SUP002', 'Mécanisme défaillant', 'agent', 'AGT002', 5);  -- Imprimante RH → AGT002 (même service RH)

-- RÉCLAMATIONS RÉSOLUES (historique)
INSERT INTO reclamation (id, date_reclamation, motif, type, statut, date_resolution, support_matricul, motif_support, user_role, id_user, materiel_id) VALUES
('REC005', '2024-08-10', 'Lenteur système, freezes fréquents', 'software', 'resolu', '2024-08-12', 'SUP001', 'Nettoyage disque et optimisation système effectués', 'agent', 'AGT002', 2),  -- PC Fatima (service RH)
('REC006', '2024-08-05', 'Projecteur ne s allume plus', 'hardware', 'resolu', '2024-08-06', 'SUP002', 'Ampoule remplacée', 'agent', 'AGT004', 7);  -- Projecteur RH → AGT004 (même service RH)

-- RÉCLAMATIONS PAR SUPPORT (peuvent réclamer pour tous les équipements)
INSERT INTO reclamation (id, date_reclamation, motif, type, statut, user_role, id_user, materiel_id) VALUES
('REC007', '2024-09-13', 'PC admin ne démarre pas', 'hardware', 'en_attente', 'admin', 'ADM001', 4),  -- PC Admin (service Direction)
('REC008', '2024-09-14', 'Serveur surchauffe anormalement', 'hardware', 'en_attente', 'support', 'SUP001', 8),  -- Serveur (SUP001 peut réclamer - tous services)
('REC009', '2024-09-15', 'Switch réseau intermittent', 'hardware', 'en_attente', 'support', 'SUP002', 9);  -- Switch (SUP002 peut réclamer - tous services)

-- =============================
-- 5. NOTIFICATIONS - CORRIGÉES
-- =============================
INSERT INTO notification (id, user_role, user_id, sujet, message, lien, type, type_id, creator_role, creator_id) VALUES
('NOTIF007', 'agent', 'AGT003', 'Réclamation en attente', 'Votre réclamation REC001 est en attente', '/reclamations/historique', 'reclamation', 'REC001', 'support', 'SUP001'),
('NOTIF008', 'agent', 'AGT002', 'Réclamation en attente', 'Votre réclamation REC002 est en attente', '/reclamations/historique', 'reclamation', 'REC002', 'support', 'SUP002'),
('NOTIF009', 'agent', 'AGT001', 'Demande de maintenance', 'Votre réclamation REC003 est en cours de résolution', '/reclamations/historique', 'reclamation', 'REC003', 'support', 'SUP001'),
('NOTIF010', 'agent', 'AGT002', 'Demande de maintenance', 'Votre réclamation REC004 est en cours de résolution', '/reclamations/historique', 'reclamation', 'REC004', 'support', 'SUP002'),
('NOTIF011', 'agent', 'AGT002', 'Réclamation résolue', 'Votre réclamation REC005 a été résolue avec succès', '/reclamations/historique', 'reclamation', 'REC005', 'support', 'SUP001'),
('NOTIF012', 'agent', 'AGT004', 'Réclamation résolue', 'Votre réclamation REC006 a été résolue avec succès', '/reclamations/historique', 'reclamation', 'REC006', 'support', 'SUP002'),
('NOTIF013', 'admin', 'ADM001', 'Réclamation en attente', 'Votre réclamation REC007 est en attente', '/reclamations/historique', 'reclamation', 'REC007', 'support', 'SUP001'),
('NOTIF014', 'support', 'SUP001', 'Réclamation créée', 'Votre réclamation REC008 a été enregistrée', '/reclamations/historique', 'reclamation', 'REC008', 'support', 'SUP001'),
('NOTIF015', 'support', 'SUP002', 'Réclamation créée', 'Votre réclamation REC009 a été enregistrée', '/reclamations/historique', 'reclamation', 'REC009', 'support', 'SUP002');

-- =============================
-- 6. UTILISATEUR BLOQUÉ
-- =============================
INSERT INTO agent (matricul, nom, prenom, tache, date_de_debut, post, email, password, N_T, nombre_tentative, bloque, service_id, salle_id) VALUES
('AGT007', 'Test', 'Bloque', 'Test User', '2024-01-01', 'Test', 'test.bloque@company.ma', '$2y$10$hashtest', 0, 5, TRUE, 3, 3);