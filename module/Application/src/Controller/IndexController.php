<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController {
	public function indexAction() {
		$this->convertCurrency( 1 );

		return new ViewModel();
	}

	public function convertAction() {
		if ( ! $this->request->isXmlHttpRequest() ) {
			return $this->redirect()->toUrl( '/' );
		}

		$jm = new JsonModel();
		$jm->setTerminal( true );

		try {
			$source = $this->params()->fromPost( 'source' );
			$target = $this->convertCurrency( $source );
			$jm->setVariable( 'success', true );
			$jm->setVariable( 'source', $source );
			$jm->setVariable( 'target', $target );
		} catch ( \Exception $e ) {
			$jm->setVariable( 'success', false );
			$jm->setVariable( 'msg', $e->getMessage() );
		}

		return $jm;
	}

	/**
	 * @param int $amount
	 * @param string $from
	 * @param string $to
	 *
	 * @throws \Exception
	 */
	private function convertCurrency( $amount, $from = 'RUB', $to = 'PLN' ) {
		try {
			$client = new Client();
			$client->setAdapter( new Curl() );
			$client->setUri( "https://free.currencyconverterapi.com/api/v5/convert?q={$from}_{$to}&compact=y" );
			$client->send();
			$data     = $client->getResponse();
			$currency = json_decode( $data->getContent() );

			return $amount * $currency->RUB_PLN->val;
		} catch ( \Exception $e ) {
			throw new \Exception( "Unable to convert" );
		}
	}
}
