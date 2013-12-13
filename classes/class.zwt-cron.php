<?php

if( $_SERVER[ 'SCRIPT_FILENAME' ] == __FILE__ )
	die( 'Access denied.' );

if( !class_exists( 'ZWT_Cron' ) )
{
	/**
	 * Handles cron jobs and intervals
	 * Note: Because WP-Cron only fires hooks when HTTP requests are made, make sure that an external monitoring service pings the site regularly to ensure hooks are fired frequently
	 * 
	 * @package ZWT_Base
	 * @author Zanto Translate
	 */
	class ZWT_Cron extends ZWT_Module
	{
		protected static $readableProperties	= array();
		protected static $writeableProperties	= array();
		
		/*
		 * Magic methods
		 */
		
		/**
		 * Constructor
		 * @mvc Controller
		 * @author Zanto Translate
		 */
		protected function __construct()
		{
			$this->registerHookCallbacks();
		}
		
		
		/*
		 * Static methods
		 */
		
		/**
		 * Adds custom intervals to the cron schedule.
		 * @mvc Model
		 * @author Zanto Translate
		 * @param array $schedules
		 * @return array
		 */
		public static function addCustomCronIntervals( $schedules )
		{
			$schedules[ ZWT_Base::PREFIX . 'debug' ] = array(
				'interval'	=> 5,
				'display'	=> 'Every 5 seconds'
			);
			
			$schedules[ ZWT_Base::PREFIX . 'ten_minutes' ] = array(
				'interval'	=> 60 * 10,
				'display'	=> 'Every 10 minutes'
			);
			
			$schedules[ ZWT_Base::PREFIX . 'example_interval' ] = array(
				'interval'	=> 60 * 60 * 5,
				'display'	=> 'Every 5 hours'
			);

			return $schedules;
		}
		
		/**
		 * Fires a cron job at a specific time of day, rather than on an interval
		 * @mvc Controller
		 * @author Zanto Translate
		 */
		public static function fireJobAtTime()
		{
			if( did_action( ZWT_Base::PREFIX . 'cron_timed_jobs' ) !== 1 )
				return;
				
			$now = current_time( 'timestamp' );
			
			// Example job to fire between 1am and 3am
			if( (int) date( 'G', $now ) >= 1 && (int) date( 'G', $now ) <= 3 )
			{
				if( !get_transient( ZWT_Base::PREFIX . 'cron_example_timed_job' ) )
				{
					//zwtCPTExample::exampleTimedJob();
					set_transient( ZWT_Base::PREFIX . 'cron_example_timed_job', true, 60 * 60 * 6 );
				}
			}
		}
		
		/**
		 * Example WP-Cron job
		 * @mvc Model
		 * @author Zanto Translate
		 * @param array $schedules
		 * @return array
		 */
		public static function exampleJob()
		{
			if( did_action( ZWT_Base::PREFIX . 'cron_example_job' ) !== 1 )
				return;
			
			// Do stuff
			
			ZWT_Base::$notices->enqueue( __METHOD__ . ' cron job fired.' );
		}
		
		
		/*
		 * Instance methods
		 */
		 
		/**
		 * Register callbacks for actions and filters
		 * @mvc Controller
		 * @author Zanto Translate
		 */
		public function registerHookCallbacks()
		{
			// NOTE: Make sure you update the did_action() parameter in the corresponding callback method when changing the hooks here
			add_action( ZWT_Base::PREFIX . 'cron_timed_jobs',	__CLASS__ . '::fireJobAtTime' );
			add_action( ZWT_Base::PREFIX . 'cron_example_job',	__CLASS__ . '::exampleJob' );
			add_action( 'init',	 array( $this, 'init' ) );
			
			add_filter( 'cron_schedules',	__CLASS__ . '::addCustomCronIntervals' );
		}
		
		/**
		 * Prepares site to use the plugin during activation
		 * @mvc Controller
		 * @author Zanto Translate
		 * @param bool $networkWide
		 */
		public function activate()
		{
			if( wp_next_scheduled( ZWT_Base::PREFIX . 'cron_timed_jobs' ) === false )
			{
				wp_schedule_event(
					current_time( 'timestamp' ),
					ZWT_Base::PREFIX . 'ten_minutes',
					ZWT_Base::PREFIX . 'cron_timed_jobs'
				);
			}
				
			if( wp_next_scheduled( ZWT_Base::PREFIX . 'cron_example_job' ) === false )
			{
				wp_schedule_event(
					current_time( 'timestamp' ),
					ZWT_Base::PREFIX . 'example_interval',
					ZWT_Base::PREFIX . 'cron_example_job'
				);
			}
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 * @mvc Controller
		 * @author Zanto Translate
		 */
		public function deactivate()
		{
			wp_clear_scheduled_hook( ZWT_Base::PREFIX . 'timed_jobs' );
			wp_clear_scheduled_hook( ZWT_Base::PREFIX . 'example_job' );
		}
		
		/**
		 * Initializes variables
		 * @mvc Controller
		 * @author Zanto Translate
		 */
		public function init()
		{
			if( did_action( 'init' ) !== 1 )
				return;
		}
		
		/**
		 * Executes the logic of upgrading from specific older versions of the plugin to the current version
		 * @mvc Model
		 * @author Zanto Translate
		 * @param string $dbVersion
		 */
		public function upgrade( $dbVersion = 0 )
		{
			/*
			if( version_compare( $dbVersion, 'x.y.z', '<' ) )
			{
				// Do stuff
			}
			*/
		}

		/**
		 * Checks that the object is in a correct state
		 * @mvc Model
		 * @author Zanto Translate
		 * @param string $property An individual property to check, or 'all' to check all of them
		 * @return bool
		 */
		protected function isValid( $property = 'all' )
		{
			return true;
		}
	} // end ZWT_Cron
}

?>