<?php
namespace Media\Service;

abstract class ERROR_TYPES {
    const NO_READ_PERMISSION = 0;
    const NO_WRITE_PERMISSION = 1;
    const FOLDER_ALREADY_EXISTS = 2;
    const FILE_ALREADY_EXISTS = 3;
    const FILE_NOT_FOUND = 4;
    const FOLDER_NOT_FOUND = 5;
    const PARENT_NOT_EXISTS = 6;
    const FORBIDDEN_NAME = 7;
    const MEDIA_ITEM_NOT_FOUND = 8;
    const ERROR_RENAMING_FOLDER = 9;
    const ERROR_RENAMING_FILE = 10;
    const FORBIDDEN_CHAR_SLASH = 11;
    const TARGET_FOLDER_NOT_FOUND = 12;
    const TARGET_NO_WRITE_PERMISSION = 13;
    const TARGET_ALREADY_EXISTS = 14;
    const ERROR_MOVING_FOLDER = 15;
    const ERROR_MOVING_FILE = 16;
    const ERROR_DELETE_FOLDER = 17;
    const ERROR_DELETE_FILE = 18;
    const ERROR_COPYING_FOLDER = 19;
    const ERROR_COPYING_FILE = 20;
    const ERROR_READING_FILE = 21;
    const ERROR_WRITING_FILE = 22;
    const CAN_NOT_READ_FOLDER = 23;
    const CAN_NOT_WRITE_FOLDER = 24;
    const NO_ZIP_EXTENSION = 25;
    const ERROR_IN_ZIP = 26;
    const NO_READ_PERMISSION_IN_CHILDS = 27;
    const NO_WRITE_PERMISSION_IN_CHILDS = 28;
    const ERROR_FOLDER_NOT_IN_DATA_PATH = 29;
    const UPLOAD_ERROR = 30;
    const UPLOAD_FILE_NOT_FOUND = 31;
    const FILE_UPLOAD_ERROR = 32;

}