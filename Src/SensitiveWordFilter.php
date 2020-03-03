<?php

namespace Sensitive;

use Generator;

class SensitiveWordFilter
{
	/**
	 * @var null 实例
	 */
	private static $oInstance = NULL;

	/**
	 * @var string 替换字符
	 */
	private static $sSymbol;

	/**
	 * @var array 敏感词汇树
	 */
	private static $aSensitiveTree = [];

	/**
	 * @var array 敏感词汇树
	 */
	private static $sFilePath = './Resource/SensitiveWords.txt';

	/**
	 * SensitiveWordFilter constructor. 构造函数
	 *
	 * @param string $sSymbol
	 */
	private function __construct( string $sSymbol )
	{
		self::$sSymbol = $sSymbol;
	}

	/**
	 * 获取实例
	 *
	 * @param string $sSymbol
	 *
	 * @return SensitiveWordFilter|null
	 */
	public static function getInstance( string $sSymbol = '*' )
	{
		if (!(self::$oInstance instanceof SensitiveWordFilter)) {
			return self::$oInstance = new self($sSymbol);
		}

		return self::$oInstance;
	}

	/**
	 * 添加敏感词，组成树结构。
	 * 例如敏感词为：傻子是傻帽，白痴，傻蛋 这组词组成如下结构。
	 * [
	 *     [傻] => [
	 *           [子]=>[
	 *               [是]=>[
	 *                  [傻]=>[
	 *                      [帽]=>[false]
	 *                  ]
	 *              ]
	 *          ],
	 *          [蛋]=>[false]
	 *      ],
	 *      [白]=>[
	 *          [痴]=>[false]
	 *      ]
	 *  ]
	 *
	 * @param string $sFilePath 敏感词库文件路径
	 */
	public static function addSensitiveWords( string $sFilePath = '' )
	: void
	{
		$sFilePath = empty($sFilePath) ? self::$sFilePath : $sFilePath;

		foreach ( self::readFile($sFilePath) as $sWords ) {
			$iLen     = mb_strlen($sWords);
			$aTreeArr = &self::$aSensitiveTree;

			for ( $iI = 0; $iI < $iLen; $iI++ ) {
				$sWord = mb_substr($sWords, $iI, 1);
				// 敏感词树结尾记录状态为false；
				$aTreeArr = &$aTreeArr[$sWord] ?? $aTreeArr = FALSE;
			}
		}

		// 将所有的单个字符去除
		foreach ( self::$aSensitiveTree as $sItem => &$mVal ) {
			if (!is_array($mVal)) {
				unset(self::$aSensitiveTree[$sItem]);
			}
		}
	}

	/**
	 * 执行过滤
	 *
	 * @param string $sTxt
	 *
	 * @return string
	 */
	public static function execFilter( string $sTxt )
	: string
	{
		$aWordList = self::searchWords($sTxt);

		if (empty($aWordList))
			return $sTxt;

		return strtr($sTxt, $aWordList);
	}

	/**
	 * 搜索敏感词
	 *
	 * @param string $sTxt
	 *
	 * @return array
	 */
	private static function searchWords( string $sTxt )
	: array
	{
		$iTxtLength = mb_strlen($sTxt);
		$aWordList  = [];

		for ( $iI = 0; $iI < $iTxtLength; $iI++ ) {
			//检查字符是否存在敏感词树内,传入检查文本、搜索开始位置、文本长度
			$iLen = self::checkWordTree($sTxt, $iI, $iTxtLength);

			//存在敏感词，进行字符替换。
			if ($iLen > 0) {
				//搜索出来的敏感词
				$sWord             = mb_substr($sTxt, $iI, $iLen);
				$aWordList[$sWord] = str_repeat(self::$sSymbol, $iLen);

				$iI = $iLen - 1 + $iI;
			}
		}

		return $aWordList;
	}

	/**
	 * 检查敏感词树是否合法
	 *
	 * @param string $sTxt
	 * @param int    $iIndex
	 * @param int    $iTxtLength
	 *
	 * @return int 返回不合法字符个数
	 */
	private static function checkWordTree( string $sTxt, int $iIndex, int $iTxtLength )
	: int
	{
		$aTreeArr = &self::$aSensitiveTree;
		//敏感字符个数
		$iWordLength = 0;

		for ( $iI = $iIndex; $iI < $iTxtLength; $iI++ ) {
			//截取需要检测的文本，和词库进行比对
			$sTxtWord = mb_substr($sTxt, $iI, 1);

			//如果搜索字不存在词库中直接停止循环
			if (!isset($aTreeArr[$sTxtWord])) {
				if ($iWordLength > 0) {
					$iWordLength = 0;
				}
				break;
			}

			//检测还未到底
			if ($aTreeArr[$sTxtWord] !== FALSE) {
				//继续搜索下一层tree
				$aTreeArr = &$aTreeArr[$sTxtWord];
				$iWordLength++;
			} else if ($aTreeArr[$sTxtWord] === FALSE) {
				$iWordLength++;
				break;
			}
		}

		// 匹配到一个字符, 不设为敏感字符
		if ($iWordLength <= 1) {
			$iWordLength = 0;
		}

		return $iWordLength;
	}

	/**
	 * 读取文件内容
	 *
	 * @param string $sFilePath
	 *
	 * @return Generator
	 */
	private static function readFile( string $sFilePath )
	: Generator
	{
		$oHandle = fopen($sFilePath, 'r');

		while (!feof($oHandle)) {
			yield trim(fgets($oHandle));
		}

		fclose($oHandle);
	}

	/**
	 * 禁止clone对象
	 * @throws \Exception
	 */
	private function __clone()
	{
		throw new \Exception("Clone instance failed!");
	}

	/**
	 * 禁止序列化对象
	 * @throws \Exception
	 */
	private function __wakeup()
	{
		throw new \Exception("UnSerialize instance failed!");
	}
}