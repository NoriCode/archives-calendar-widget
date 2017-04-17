<?php

class ARCW {

	private $datesWithPosts = array();

	function __construct( $config ) {
		global $wpdb, $wp_locale, $post;
		$this->wpdb      = $wpdb;
		$this->wp_locale = $wp_locale;

		// define the widget config
		$this->config = $config;
		// load Archives Calendar plugin options
		$this->plugin_options = get_option( 'archivesCalendar' );

		$this->post_types_str = $this->get_post_types_string();

		// init a list with all dates that have posts
		$this->dates = $this->get_calendar_dates();

		$this->activeDate = $this->get_active_date();
	}

	/*
	 * PRIVATE METHODS
	 * =================
	*/

	/**
	 * Transform the $this->config['post_type'] list of the post types into a "'{post_type}', '{post_type}'" string
	 * to be able to use it in the sql `IN` operator
	 * @return string
	 */
	private function get_post_types_string() {
		// in sql queries we need the `post_types` list in string format for the `IN` operator
		$post_types = is_array( $this->config['post_type'] ) ?
			implode( "','", $this->config['post_type'] ) :
			$this->config['post_type'];

		return "'{$post_types}'";
	}


	/**
	 * Get all years=>months=>days that have posts
	 * @return array|null|object
	 */
	private function get_calendar_dates() {

		/*
		 * Select all distinct years with months andthat have posts
		 * @var $sql
		 */
		$sql = "SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month, " .
		       "GROUP_CONCAT(DISTINCT DAY(post_date) ORDER BY post_date ASC) AS days " .
		       "FROM {$this->wpdb->posts} wpposts ";

		// add category filter
		$sql .= $this->sql_category_filter();

		// Filter the query by the post_type, select only published and not protected posts, group it and order it by year and month
		$sql .= "WHERE post_type IN ({$this->post_types_str}) " .
		        "AND post_status IN ('publish') " .
		        "AND post_password='' " .
		        "GROUP BY year,month " .
		        "ORDER BY year DESC";

		// execute the query
		$results = $this->wpdb->get_results( $sql, ARRAY_A );

		$dates = array();
		// group months by year and day by month
		foreach ( $results as $date ) {
			$dates[ $date['year'] ][ $date['month'] ] = explode( ',', $date['days'] );
		}

		return $dates;
	}


	/**
	 * Rreturn sql JOIN command to filter the results by the categories selected in the widget configuration
	 * only returns the command if categories are selected in configuration, the `All` option does not need filtering
	 * @return string
	 */
	private function sql_category_filter() {

		// if categories is array count number of items
		$categoriesCount = is_array( $this->config['categories'] ) ? count( $this->config['categories'] ) : 0;
		// for the sql we `IN` operator we need a string list with commas
		$categories = implode( ',', $this->config['categories'] );

		// if categories were defined in the configuration we have to refine the query to filter posts that only are in the specified categories
		if ( $categoriesCount ) {
			$join = "JOIN {$this->wpdb->term_relationships} tr ON wpposts.ID = tr.object_id " .
			        "JOIN {$this->wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id " .
			        "AND tt.term_id IN({$categories}) " .
			        "AND tt.taxonomy = 'category') ";
		} else {
			$join = "";
		}

		return $join;
	}


	/**
	 * Return the key of the last element of an array
	 *
	 * @param $array
	 *
	 * @return mixed
	 */
	function endKey( $array ) {
		end( $array );

		return key( $array );
	}

	/**
	 * Return the key of the first element of an array
	 *
	 * @param $array
	 *
	 * @return mixed
	 */
	function startKey( $array ) {
		reset($array);
		return key( $array );
	}

	/**
	 * Return the active date object
	 * Active date is
	 * - current date
	 * - date of currently displayed archive page
	 * - date of the last/previous/next month
	 * @return stdClass
	 */
	private function get_active_date() {
		if ( $this->config['month_view'] ) {
			return $this->get_active_month();
		} else {
			return $this->get_active_year();
		}
	}

	/**
	 * Return the active date (year and month) for the Months view
	 * It allows to display next/current/prev month on the callendar even if in archives view
	 * OR
	 * display the latest available month AND display matching month to the archives view
	 * @return stdClass
	 */
	private function get_active_month() {
		global $post;
		$activeDate = new stdClass();

		// current year and month
		$year  = intval( date( 'Y' ) );
		$month = intval( date( 'm' ) );

//      If I keep this when viewing archives the calendar will show previous month from the actual arrchive
//
//		if ( is_archive() && ! is_category() ) {
//			$year  = intval( date( 'Y', strtotime( $post->post_date ) ) );
//			$month = intval( date( 'm', strtotime( $post->post_date ) ) );
//		}

		switch ( $this->config['month_select'] ) {
			// display previous month (from today)
			case 'prev':
				// in case of first month of the year to show previous month we have decrease the year and set month to maximum
				if ( $month == 1 ) {
					$month = 12;
					$year --;
				} else {
					$month --;
				}
				break;
			// display current month (today)
			case 'current':
				break;
			// display newt month (from today)
			case 'next':
				// in case of last month of the year to show next month we have increase the year and set month to minimum
				if ( $month == 12 ) {
					$month = 1;
					$year ++;
				} else {
					$month ++;
				}
				break;

			// default is displaying the latest available month
			default:
				// if we are in archives set the year and the month to the archives ones
				if ( is_archive() && ! is_category() ) {
					$year  = intval( date( 'Y', strtotime( $post->post_date ) ) );
					$month = intval( date( 'm', strtotime( $post->post_date ) ) );

					// if the month does not exist get the latest available
					if ( ! array_key_exists( $year, $this->dates ) || ! array_key_exists( $month, $this->dates[ $year ] ) ) {
						$year  = $this->startKey( $this->dates );
						$month = $this->startKey( $this->dates[ $year ] );
					}
				} else {
					$year  = $this->startKey( $this->dates );
					$month = $this->startKey( $this->dates[ $year ] );
				}
		}

		// check if month exists in dates, if not add one empty
		$this->month_exists_or_add( $year, $month );

		//set the final active date
		$activeDate->year  = $year;
		$activeDate->month = $month;

		return $activeDate;
	}

	/**
	 * Return the active date (year) for the Years view
	 * It allows to display the current archives year on the calendar first
	 * or the last available year when not in archives view
	 * @return stdClass
	 */
	private function get_active_year() {
		global $post;
		$activeDate = new stdClass();

		// In the year view show only the latest available year
		// set year to the last year in the dates list
		$year = $this->startKey( $this->dates );

		// if currently viewing archives set the year to the matching archive year
		if ( is_archive() ) {
			$year = date( 'Y', strtotime( $post->post_date ) );

			// if there is no dates for the archive year reset it to the last one
			if ( ! array_key_exists( $year, $this->dates ) ) {
				$year = $this->startKey( $this->dates );
			}
		}

		$activeDate->year = $year;

		return $activeDate;
	}

	/**
	 * Checks if the specified month exists in the dates
	 * if not it adds an empty month so we can display it anyway in the calendar
	 *
	 * Returns false if month was not present in dates
	 *
	 * @param int $year
	 * @param int $month
	 *
	 * @return bool
	 */
	private function month_exists_or_add( $year, $month ) {
		if ( ! array_key_exists( $year, $this->dates ) || ! array_key_exists( $month, $this->dates[ $year ] ) ) {
			$this->dates[ $year ][ $month ] = array();

			return false;
		}

		return true;
	}


	/*
	 * PUBLIC METHODS
	 * =====================================================
	 * Available for templates
	*/

	/**
	 * Get the number of posts published on specified date
	 * filtered by `post_type` and `categories` set in the configuration
	 *
	 * @param $year
	 * @param $month
	 * @param null|int $day
	 *
	 * @return null|string
	 */
	function get_post_count( $year, $month, $day = null ) {

		$sql = "SELECT COUNT(DISTINCT(ID)) FROM {$this->wpdb->posts} wpposts ";

		// add category filter
		$sql .= $this->sql_category_filter();

		$sql .= "WHERE post_type IN ({$this->post_types_str}) " .
		        "AND post_status IN ('publish') " .
		        "AND post_password='' " .
		        "AND YEAR(post_date) = {$year} " .
		        "AND MONTH(post_date) = {$month}";

		if ( $day ) {
			$sql .= "AND DAY(post_date) = {$day}";
		}

		return $this->wpdb->get_var( $sql );
	}


	/**
	 * Return the calendar theme
	 * - the global one set in the plugin configuration
	 * OR
	 * - "different theme" set in the widget settings
	 * @return string
	 */
	function get_theme() {
		return $this->config['different_theme'] ? $this->config['theme'] : $this->plugin_options['theme'];
	}

	/**
	 * Return an array with items for the calendar navigation
	 * @return array
	 */
	function get_navigation_list() {
		$nav = array();

		foreach ( $this->dates as $year => $months ) {

			if ( $this->config['month_view'] ) {
				foreach ( $months as $month => $days ) {
					$nav[] = array(
						"year"  => $year,
						"month" => $month
					);
				}
			} else {
				$nav[] = array(
					"year" => $year
				);
			}
		}

		return $nav;
	}


	/**
	 * Get full list of years and months for the "year view" template
	 * All months of the year are present but only the ones with posts has `url != null` property
	 * @return array|null|object
	 */
	private function get_months() {

		$years = $this->datesWithPosts;

		// Fore each year create full month calendar
		foreach ( $years as &$year ) {
			// the moths list is a string separated with ',' (SQL GROUP_CONCAT)
			// so we need to transform it into an array
			$year['months'] = explode( ',', $year['months'] );

			// Create a new months list that will contain all months for the year
			$months = array();

			// So we create 12 months
			for ( $month = 1; $month <= 12; $month ++ ) {

				// by default the month is not set as "has posts" so no `count` or `href`
				// count is set to `0` by default if "display post count" is enabled and `null` if it is disabled
				$count = $this->config['post_count'] ? 0 : null;
				$href  = null;

				// if the month is present in the year month list set the missing data
				if ( in_array( $month, $year['months'] ) ) {
					// if "display post count" is enabled in widget config update the post count
					if ( $this->config['post_count'] ) {
						$count = $this->get_post_count( $year['year'], $month );
					};
					// get the link url
					$href = $count ? $this->filter_link( get_month_link( $year['year'], $month ) ) : null;

				};

				// Add the month to the new $months list
				$months[] = array(
					"month"      => $month,
					"name"       => $this->wp_locale->get_month_abbrev( $this->wp_locale->get_month( $month ) ),
					"post_count" => $count,
					"url"        => $href,
					"full_date"  => date_i18n( 'F, Y', strtotime( $year['year'] . '-' . $month ) )
				);

			}

			// Replace the old months list with the new one with all the data for each month
			$year['months'] = $months;
		}

		return $years;
	}


	/**
	 * Adds the ARCW filter parameters to the month/day archive url
	 * for to filter the archives pages matching the calendar settings
	 *
	 * @param $url
	 *
	 * @return string
	 */
	function filter_link( $url ) {
		$filterEnabled = $this->plugin_options['filter'];

		// We do not need do anything with links so we return it as is
		// - if filter option is not enabled in options
		// - if filter is enabled but post_type is default/undefined and no categories are selected
		if ( ! $filterEnabled || ( $filterEnabled && ( empty( $this->config['categories'] ) || $this->post_types_str == "post" ) && empty( $this->config['categories'] ) ) ) {
			return $url;
		}

		$params = array( 'arcf' => '' );
		$attr   = &$params['arcf'];

		$attr = is_array( $this->config['post_type'] ) ? implode( '/', $this->config['post_type'] ) : "{$this->config['post_type']}/";

		$attr .= $attr != '' ? '/' : '';
		$attr .= implode( '/', $this->config['categories'] );

		/* old format
		$params = array( 'arcf' => '' );
		$attr   = &$params['arcf'];
		if ( ! empty( $this->post_types_str ) && $this->post_types_str != 'post' ) {
			$attr = 'post:' . str_replace( ',', '+', $this->plugin_options['post_type'] );
		}
		if ( ! empty( $cats ) ) {
			$attr .= $attr != '' ? ':' : '';
			$attr .= 'cat:' . str_replace( ', ', '+', $this->plugin_options['categories'] );
		}
		*/

		return update_url_params( $url, $params );
	}


	/**
	 * Checks if url property is present and is not null in the date array
	 *
	 * @param $date array The calendar month/day array that contains the information about the date
	 *
	 * @return boolean
	 */
	function has_posts( $date ) {
		return $date['url'] && ! empty( $date['url'] );
	}

	function render() {

	}

}