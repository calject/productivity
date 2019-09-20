<?php
/**
 * Author: 沧澜
 * Date: 2017/11/21
 */

namespace CalJect\Productivity\Utils;

class TimeUtil
{
    /**
     * 精确到秒级单位(s)
     */
    const SCALE_SECOND = 0;
    /**
     * 精确到毫秒级单位(ms)
     */
    const SCALE_MILLISECOND = 3;
    /**
     * 精确到微秒级单位(μs)
     */
    const SCALE_MICROSECOND = 6;

    /**
     * 最大精度单位
     */
    const SCALE_MAX = 6;

    /*------------------- 日期格式 -------------------*/
    const FORMAT_YEAR = 'Y';
    const FORMAT_MONTH = 'm';
    const FORMAT_DAY = 'd';
    const FORMAT_HOUR = 'H';
    const FORMAT_MINUTE = 'i';
    const FORMAT_SECOND = 's';

    const FORMAT_YMDHIS = 'YmdHis';
    const FORMAT_YMDHIS_SHORT = 'ymdHis';
    const FORMAT_YMD = 'Ymd';
    const FORMAT_HIS = 'His';

    const FORMAT_DATE = 'Y-m-d H:i:s';
    const FORMAT_Y_M_D = 'Y-m-d';
    const FORMAT_H_I_S = 'H:i:s';

    /*
     * 获取时间戳的毫秒数部分
     * @return string
     */
    public static function getMillisecond()
    {
        list($usec, $sec) = explode(" ", microtime());
        $msec = round($usec * 1000);
        $msec = str_pad($msec, 3, '0', STR_PAD_RIGHT);
        return $msec;
    }

    /**
     * 获取完整的时间戳(精确到毫秒)
     * @return string
     */
    public static function getTimeMilliseconWhole()
    {
        return self::getTime() . self::getMillisecond();
    }

    /**
     * 获取精确到微秒的时间戳[保留到小数点后7位](单位s)
     * @return float
     */
    public static function getTimeMicrosecond()
    {
        return self::getTime(self::SCALE_MICROSECOND);
    }


    /**
     * 获取精确到毫秒的时间戳[保留到小数点后3位](单位s float)
     * @return float
     */
    public static function getTimeMillisecond()
    {
        return self::getTime(self::SCALE_MILLISECOND);
    }

    /**
     * 获取时间戳(精确到秒)
     * @param int $scale 获取的时间单位精度
     * @return float
     */
    public static function getTime($scale = self::SCALE_SECOND)
    {
        if ($scale == self::SCALE_SECOND) {
            return time();
        } else {
            list($usec, $sec) = explode(" ", microtime());
            return bcadd($sec, $usec, $scale);
        }
    }

    /**
     * 获取当前日期(Y-m-d H:i:s)
     * @param string $time 时间戳/日期
     * @return bool|string
     */
    public static function getDate($time = NULL)
    {
        return self::dateFormat(self::FORMAT_DATE, $time);
    }


    /**
     * 获取当前日期(YmdHis)
     * @param string $time 时间戳/日期
     * @return bool|string
     */
    public static function getDateYmdHis($time = NULL)
    {
        return self::dateFormat(self::FORMAT_YMDHIS, $time);
    }

    /**
     * 获取当前日期(ymdHis)
     * @param string $time
     * @return bool|string
     */
    public static function getDateYmdHisShort($time = NULL)
    {
        return self::dateFormat(self::FORMAT_YMDHIS_SHORT, $time);
    }

    /**
     * 获取当前日期(Y-m-d)
     * @param string $time 时间戳/日期
     * @return bool|string
     */
    public static function getDateY_m_d($time = NULL)
    {
        return self::dateFormat(self::FORMAT_Y_M_D, $time);
    }


    /**
     * 获取当前日期(Ymd)
     * @param string $time 时间戳/日期
     * @return bool|string
     */
    public static function getDateYmd($time = NULL)
    {
        return self::dateFormat(self::FORMAT_YMD, $time);
    }

    /**
     * 获取当前日期(H:i:s)
     * @param string $time 时间戳/日期
     * @return bool|string
     */
    public static function getDateH_i_s($time = NULL)
    {
        return self::dateFormat(self::FORMAT_H_I_S, $time);
    }

    /**
     * 获取当前日期(His)
     * @param string $time 时间戳/日期
     * @return bool|string
     */
    public static function getDateHis($time = NULL)
    {
        return self::dateFormat(self::FORMAT_HIS, $time);
    }

    /**
     * 获取时间单位(年)
     * @param string $time 时间戳/日期
     * @return bool|string
     */
    public static function getYear($time = NULL)
    {
        return self::dateFormat(self::FORMAT_YEAR, $time);
    }

    /**
     * 获取时间单位(月)
     * @param string $time 时间戳/日期
     * @return bool|string
     */
    public static function getMonth($time = NULL)
    {
        return self::dateFormat(self::FORMAT_MONTH, $time);
    }

    /**
     * 获取时间单位(日)
     * @param string $time 时间戳/日期
     * @return bool|string
     */
    public static function getDay($time = NULL)
    {
        return self::dateFormat(self::FORMAT_DAY, $time);
    }


    /**
     * 获取时间单位(小时)
     * @param null $time
     * @return bool|string
     */
    public static function getHour($time = NULL)
    {
        return self::dateFormat(self::FORMAT_HOUR, $time);
    }

    /**
     * 获取时间单位(分钟)
     * @param null $time
     * @return bool|string
     */
    public static function getMinute($time = NULL)
    {
        return self::dateFormat(self::FORMAT_MINUTE, $time);
    }

    /**
     * 获取时间单位(秒)
     * @param null $time
     * @return bool|string
     */
    public static function getSecond($time = NULL)
    {
        return self::dateFormat(self::FORMAT_SECOND, $time);
    }

    /**
     * 转换为时间戳
     * @param string $val
     * @return int
     */
    public static function toTime($val = null)
    {
        empty($val) && $val = time();
        if (!is_numeric($val)) {
            $val = strtotime($val);
        }
        return $val;
    }


    /**
     * 日期格式化
     * @param string $format
     * @param null $time
     * @return bool|string
     */
    protected static function dateFormat($format = self::FORMAT_DATE, $time = NULL)
    {
        $time = self::toTime($time);
        return date($format, $time);
    }

    /*---------------------------------------------- Calculations function ----------------------------------------------*/

    /**
     * 获取当日最后时间（Y-m-d 23:59:59)
     * @param string $time 时间戳/日期
     * @return bool|string
     */
    public static function getTodayEnd($time = NULL)
    {
        return self::getDateY_m_d($time)." 23:59:59";
    }

    /**
     * 获取今日剩余秒
     * @return int
     */
    public static function getTodayRemainTime()
    {
        $today_end = self::getTodayEnd();
        return strtotime($today_end) - time();
    }

    /**
     * 获取今日剩余分钟数
     * @return float
     */
    public static function getTodayRemainMinute()
    {
        return self::getTodayRemainTime()/60;
    }



}