<?php

namespace Kiskadi\Api\Client;

use Exception;
use Kiskadi\Api\Auth\Admin_Auth;
use Kiskadi\Api\Auth\Auth;
use Kiskadi\Api\Response\Response;
use Kiskadi\Api\Response\Response_Factory;
use Kiskadi\Integration\WooCommerce\Admin\Kiskadi_Integration_Admin;
use Kiskadi\Integration\WooCommerce\Log\Logger;
use WP_Http;
use WP_HTTP_Requests_Response;

class Client {

	public const URL = 'https://inprod.kiskadi.com/api/v2';

	/** @var Auth */
	private $auth;

	/** @var Logger */
	private $logger;

	public function __construct() {
		$this->auth   = $this->auth();
		$this->logger = new Logger();
	}

	private function auth() : Auth {
		$settings = new Kiskadi_Integration_Admin();
		$auth     = new Admin_Auth( $settings );

		return $auth;
	}

	/** @param array<mixed> $data */
	public function get( string $url, array $data = array() ) : Response {
		return $this->request( 'GET', $url, $data );
	}

	/** @param array<mixed> $data */
	public function post( string $url, array $data = array() ) : Response {
		return $this->request( 'POST', $url, $data );
	}

	/**
	 * @throws Exception
	 * @param array<mixed> $data
	 */
	private function request( string $method, string $endpoint, array $data = array() ) : Response {
		$url = self::URL . $endpoint;

		$wp_http_response = $this->run_request( $url, $method, $data );

		$response_info = $this->get_response_info( $wp_http_response );
		$status        = intval( $response_info['status'] );

		$is_authorized = $this->is_authorized( $status );
		if ( false === $is_authorized ) {
			throw new Exception( __( 'Invalid username and/or password', 'kiskadi' ) );
		}

		$data     = strval( $response_info['data'] );
		$response = ( new Response_Factory() )->create( $status, $data );

		return $response;
	}

	/**
	 * @throws Exception
	 * @param array<mixed> $data
	 * @return array<mixed>
	 */
	private function run_request( string $url, string $method, array $data = array() ) : array {
		$body = $this->convert_data_to_body( $data );
		$this->log( "REQUEST {$method} {$url} {$body}" );

		if ( 'GET' === $method ) {
			$body = $data;
		}

		$http     = new WP_Http();
		$response = $http->request(
			$url,
			array(
				'method'  => $method,
				'timeout' => 30,
				'body'    => $body,
				'headers' => array(
					'Authorization' => $this->auth->authorization(),
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
				),
			)
		);

		if ( true === is_wp_error( $response ) ) {
			throw new Exception( __( 'Request to Kiskadi API failed.', 'kiskadi' ) );
		}

		$this->log( "RESPONSE {$response['response']['code']} {$response['body']}" );

		return $response;
	}

	private function log( string $message ) : void {
		if ( null === $this->logger ) {
			return;
		}

		$message = $this->mask_sensitive_property( 'cnpj', $message );
		$message = $this->mask_sensitive_property( 'cpf', $message );
		$message = $this->mask_sensitive_property( 'email', $message );

		$this->logger->log( $message );
	}

	private function mask_sensitive_property( string $property, string $message ) : string {
		$message = preg_replace( "/(\"{$property}\":\")(.*?)(\")/", '$1xxx$3', $message );

		if ( null === $message ) {
			/* translators: The consumer property name */
			throw new Exception( sprintf( __( 'Cannot mask %s.', 'kiskadi' ), $property ) );
		}

		return $message;
	}

	/**
	 * @throws Exception
	 * @param array<mixed> $data
	 */
	private function convert_data_to_body( array $data ) : string {
		if ( 0 === count( $data ) ) {
			return '';
		}

		$body = wp_json_encode( $data );

		if ( false === $body ) {
			throw new Exception( __( 'Cannot encode request data.', 'kiskadi' ) );
		}

		return $body;
	}

	/**
	 * @throws Exception
	 * @param array<string, mixed> $response
	 * @return array<string, int|string> $data
	 */
	private function get_response_info( array $response ) {
		$http_response = $response['http_response'];

		if ( false === ( $http_response instanceof WP_HTTP_Requests_Response ) ) {
			throw new Exception( __( 'Response is not a WP_HTTP_Requests_Response object.', 'kiskadi' ) );
		}

		return array(
			'status' => intval( $http_response->get_status() ),
			'data'   => strval( $http_response->get_data() ),
		);
	}

	private function is_authorized( int $status ) : bool {
		return 401 !== $status;
	}
}
