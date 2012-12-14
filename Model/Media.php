<?php

/**
 * Media entry (photo) added to Instagram. Maps to the /media API endpoint.
 *
 * @package InstagramDatasource
 * @author Michael Enger <mien@nodesagency.no>
 * @see http://instagram.com/developer/endpoints/media/
 */
class Media extends InstagramDatasourceAppModel {

/**
 * The database configuration to use.
 *
 * @var string
 */
	public $useDbConfig = 'instagram';

/**
 * Schema describing the model.
 *
 * @var array
 */
	protected $_schema = array(
		'id' => array(
			'type' => 'string',
            'null' => false,
            'length' => 255
		),
		'type' => array(
			'type' => 'string',
            'null' => false,
            'length' => 255,
		),
		'filter' => array(
			'type' => 'string',
            'null' => false,
            'length' => 255,
		),
		'tags' => array(
			'type' => 'array',
            'null' => false,
		),
		'comments' => array(
			'type' => 'array',
            'null' => false,
		),
		'caption' => array(
			'type' => 'array',
            'null' => false,
		),
		'likes' => array(
			'type' => 'array',
            'null' => false,
		),
		'link' => array(
			'type' => 'string',
            'null' => true,
            'length' => 255,
		),
		'user' => array(
			'type' => 'array',
            'null' => false,
		),
		'created_time' => array(
			'type' => 'integer',
            'null' => false,
		),
		'images' => array(
			'type' => 'array',
            'null' => false,
		),
		'location' => array(
			'type' => 'string',
            'null' => true,
            'length' => 255,
		)
	);

}
