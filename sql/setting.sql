-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-05-15 01:25:54
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- データベース: `jitech_user`
--

CREATE DATABASE jitech_user;
use jitech_user

-- --------------------------------------------------------

--
-- テーブルの構造 `userlist`
--

CREATE TABLE `userlist` (
  `user` varchar(50) DEFAULT NULL,
  `pw` varchar(255) DEFAULT NULL,
  `role` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `userlist`
--

INSERT INTO `userlist` (`user`, `pw`, `role`) VALUES ('kaji', '74cacc47c16301658d74c75cef0470805ce63d9762d044b57bab13f46df7afc7', 'admin');
COMMIT;

--
-- データベース: `jitech`
--
CREATE DATABASE jitech;
use jitech
-- --------------------------------------------------------

--
-- テーブルの構造 `dir`
--

CREATE TABLE `dir` (
  `no` int(11) NOT NULL,
  `parentDir` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `dir`
--

INSERT INTO `dir` (`parentDir`) VALUES ('about');

-- --------------------------------------------------------

--
-- テーブルの構造 `overview`
--

CREATE TABLE `overview` (
  `no` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `overview`
--

INSERT INTO `overview` (`title`, `description`, `hidden`) VALUES ('管理者ページについて', NULL, NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `time`
--

CREATE TABLE `time` (
  `no` int(11) NOT NULL,
  `postTime` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `time`
--

INSERT INTO `time` (`postTime`) VALUES (UNIX_TIMESTAMP());

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `dir`
--
ALTER TABLE `dir` ADD PRIMARY KEY (`no`);

--
-- テーブルのインデックス `overview`
--
ALTER TABLE `overview` ADD PRIMARY KEY (`no`);

--
-- テーブルのインデックス `time`
--
ALTER TABLE `time` ADD PRIMARY KEY (`no`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `dir`
--
ALTER TABLE `dir` MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- テーブルの AUTO_INCREMENT `overview`
--
ALTER TABLE `overview` MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- テーブルの AUTO_INCREMENT `time`
--
ALTER TABLE `time`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;
COMMIT;