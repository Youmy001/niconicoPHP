<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/11/29
 * Time: 19:30
 */

namespace NiconicoPHP;


abstract class RankingCategory extends Enum {
	
	const All = 'all';
	
	const Entertainment = 'ent';
	const EntertainmentGlobal = 'g_ent2';
	const Music = 'music';
	const SangIt = 'sing';
	const PlayedIt = 'play';
	const DancedIt = 'dance';
	const Vocaloid = 'vocaloid';
	const NicoIndies = 'nicoindies';
	
	const LifeStyle = 'g_life2';
	const Animal = 'animal';
	const Cooking = 'cooking';
	const Nature = 'nature';
	const Travel = 'travel';
	const Sport = 'sport';
	const Lecture = 'lecture';
	const Driving = 'driving';
	const History = 'history';
	
	const Politics = 'g_politics';
	
	const ScienceTechnology = 'g_tech';
	const Science = 'science';
	const Technology = 'tech';
	const Handcraft = 'handcraft';
	const BuiltIt = 'make';
	
	const Culture = 'g_culture2';
	const Anime = 'anime';
	const Gaming = 'game';
	const LiveGaming = 'jikkyo';
	const Touhou = 'toho';
	const IdolMaster = 'imas';
	const Radio = 'radio';
	const DrewIt = 'draw';
	
	const OtherGlobal = 'g_other';
	const Other = 'other';
	const Diary = 'diary';
	const ThatThing = 'are';
	
	const R18 = 'g_r18';
	
}