<?php
const HTTP_OK = 200;
const HTTP_BAD_REQUEST = 400;

const USER_NOT_FOUND = 'این کاربر در سیستم ثبت نشده است!';
const USERNAME_ALREADY_EXISTS = 'این کاربر قبلا در سیستم ثبت شده است!';
const PASSWORD_MISMATCH = 'رمز عبور نادرست است!';
const CANNOT_UPLOAD_FOR_THIS_ENTITY = 'شما نمی توانید برای این موجودیت، فایل آپلود کنید!';
const ALLOWED_ENTITY_TYPES = [1,2,3];
const ALLOWED_ENTITY_SIZE = 30000000;

const UPLOAD_ARTICLE_PHOTO_FILE_TYPES = 'png|jpg|jpeg|bmp';
const MAX_UPLOAD_ARTICLE_PHOTO_FILE_SIZE =5000000;
const USERS_WHO_CAN_UPLOAD_ARTICLE_PHOTO = [1];

const UPLOAD_ARTICLE_VIDEO_FILE_TYPES = 'avi';
const MAX_UPLOAD_ARTICLE_VIDEO_FILE_SIZE = 50000000;
const USERS_WHO_CAN_UPLOAD_ARTICLE_VIDEO = [1] ;

const UPLOAD_ARTICLE_PDF_FILE_TYPES = 'pdf|txt|doc|docx';
const MAX_UPLOAD_ARTICLE_PDF_FILE_SIZE = 5000000;
const USERS_WHO_CAN_UPLOAD_PDF = [1];

const FILE_UPLOADER_ERROR_GENERAL = 'خطا در آپلود فایل درخواستی: ';
const FILE_UPLOADER_ERROR_SIZE = 'حجم فایل آپلود شده بیشتر از مقدار تعریف شده است. حجم مجاز: ';
const FILE_UPLOADER_ERROR_FORMAT = "فرمت فایل مجاز نیست.";
const FILE_UPLOADER_ERROR_DB_INSERTION = "اضافه کردن فایل به دیتابیس مقدور نیست.";
