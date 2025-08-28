<?php
	const MISSING_NUMBER = 999;

	/*
		Category
		Name: INTRINSIC TRAITS
		Hash: 3956125808
		Desc: The base qualities of a weapon.
		Indexes: 0 (aka it is the first socket in the list, same logic follows below)
	*/
	const SOCKET_INTRINSICS = 3956125808;

	/*
		Category
		Name: WEAPON PERKS
		Hash: 4241085061
		Desc: Perks are built in to a given weapon. They can be swapped out an unlimited number of times.
		Indexes: 1, 2, 3, 4, 8 (origin trait), 9 (kill tracker)
	*/
	const SOCKET_ARROWS = 1829212646;
	const SOCKET_BARRELS = 3362409147;
	const SOCKET_BATTERIES = 930927103;
	const SOCKET_BLADES = 193067693;
	const SOCKET_BOLTS = 3571716316;
	const SOCKET_BOWSTRINGS = 3546774010;
	const SOCKET_FRAMES_1 = 1215804697;
	const SOCKET_FRAMES_2 = 1215804696;
	const SOCKET_FRAMES_3 = 1215804699;
	const SOCKET_FRAMES_4 = 2614797986;
	const SOCKET_FRAMES_5 = 3530202021;
	const SOCKET_GRIPS = 514622187;
	const SOCKET_GUARDS = 1218454666;
	const SOCKET_HAFTS = 1048457342;
	const SOCKET_MAGAZINES = 3815406785;
	const SOCKET_MAGAZINES_GL = 3815406785;
	const SOCKET_MAGAZINES_GL_UNIQUE = 4246926293; // for some reason this has another entry lower down
	const SOCKET_RAILS = 1536355127;
	const SOCKET_SCOPES = 1283453667;
	const SOCKET_STOCKS = 2575784089; // for some reason nthis also includes smth called "origins"
	const SOCKET_TUBES = 1656112293;
	const SOCKET_ORIGIN_TRAIT = 3993098925;
	/*
		- these sockets have plugsets (randomizedPlugSetHash found in DestinyPlugSetDefinition json)
		- those plugsets have reusablePlugItems (array of plugItemHash which are all of type DestinyInventoryItemDefinition)
		- the DestinyInventoryItemDefinition version of a perk then has a property called "perks", inside of which is the actual DestinySandboxPerkDefinition
		- that being said you don't need to go that deep cause the description, icon, and name are all in the DestinyInventoryItemDefinition version
	*/

	/*
		Category
		Name: WEAPON COSMETICS
		Hash: 2048875504
		Desc: Attach a cosmetic to a weapon to customize its appearance.
		Indexes: 5
	*/	
	const SOCKET_SHADER = 1288200359;

	/*
		Category
		Name: WEAPON MODS
		Hash: 2685412949
		Desc: Attach a mod to a weapon to improve or add to its perks.
		Indexes: 6 (the actual mod lol), 7 (masterwork), 11 (memento), 12 (deepsight), 13 (level boost)

		Note: 
		Different weapons have different values for the SocketType hash here
		so it's better to use the SocketCategory to find the index of the mod slot you want, and then
		look at reusablePlugItems at that index to get the actual mods available
	*/
	const SOCKET_CATEGORY_WEAPON_MOD = 2685412949;	

	/*
		Category...?
		Name: ???
		Hash: 3583996951
		Desc: ???
		Indexes: 10
		Socket 10 belongs to a category with no name nor description. It might be crafting related.
		Type says: crafting.plugs.frame_identifiers
	*/
	const SOCKET_CRAFTING = 3583996951;
	
	const BUCKET_WEAPONS_KINETIC = 1498876634;
	const BUCKET_WEAPONS_ENERGY = 2465295065;
	const BUCKET_WEAPONS_POWER = 953998645;
	const BUCKET_MODIFICATIONS = 3313201758;
	const BUCKET_CONSUMABLES = 1469714392; // For some reason weaponperks are in here. Wtf man.
	const BUCKET_WEAPON_MODS = 2422292810; // For some reason this has no name in the BucketTypes json.

	const WEAPON_BUCKETS = [BUCKET_WEAPONS_KINETIC, BUCKET_WEAPONS_ENERGY, BUCKET_WEAPONS_POWER];

	/*
		This includes Enhanced weapon mods, like the ones that expire at the end of a season
	*/
	const ITEMCATEGORY_MODIFICATIONS = 59;
	/*
		regex: type_weapon_mod_all
		Weapon Mods: Damage
		These are mods that affect the damage of your weapon, in type and/or amount.
	*/
	const ITEMCATEGORY_WEAPON_MODS_DMG = 1052191496;
	/*
		Weapon Mods
		Mods that can be applied to weapons.
	*/
	const ITEMCATEGORY_WEAPON_MODS = 610365472;

	const WEAPON_MODS_ITEMCATEGORIES = [ITEMCATEGORY_MODIFICATIONS, ITEMCATEGORY_WEAPON_MODS_DMG, ITEMCATEGORY_WEAPON_MODS];


	const ITEMCATEGORY_SEASONAL_ARTIFACT = 1378222069;

	/*
		Armor Equip Blocks
	*/
	const EQUIP_BLOCK_HEAD = 3448274439;
	const EQUIP_BLOCK_ARMS = 3551918588;
	const EQUIP_BLOCK_CHEST = 14239492;
	const EQUIP_BLOCK_LEGS = 20886954;
	const EQUIP_BLOCK_CLASS = 1585787867;



	/*
DestinyClassDefinition
	{
	  "671679327": {
	    "classType": 1,
	    "displayProperties": {
	      "name": "Hunter",
	      "iconHash": 0,
	      "hasIcon": false
	    },
	    "genderedClassNames": {
	      "Male": "Hunter",
	      "Female": "Hunter"
	    },
	    "genderedClassNamesByGenderHash": {
	      "2204441813": "Hunter",
	      "3111576190": "Hunter"
	    },
	    "hash": 671679327,
	    "index": 1,
	    "redacted": false,
	    "blacklisted": false
	  },
	  "2271682572": {
	    "classType": 2,
	    "displayProperties": {
	      "name": "Warlock",
	      "iconHash": 0,
	      "hasIcon": false
	    },
	    "genderedClassNames": {
	      "Male": "Warlock",
	      "Female": "Warlock"
	    },
	    "genderedClassNamesByGenderHash": {
	      "2204441813": "Warlock",
	      "3111576190": "Warlock"
	    },
	    "hash": 2271682572,
	    "index": 2,
	    "redacted": false,
	    "blacklisted": false
	  },
	  "3655393761": {
	    "classType": 0,
	    "displayProperties": {
	      "name": "Titan",
	      "iconHash": 0,
	      "hasIcon": false
	    },
	    "genderedClassNames": {
	      "Male": "Titan",
	      "Female": "Titan"
	    },
	    "genderedClassNamesByGenderHash": {
	      "2204441813": "Titan",
	      "3111576190": "Titan"
	    },
	    "hash": 3655393761,
	    "index": 0,
	    "redacted": false,
	    "blacklisted": false
	  }
	}
	*/