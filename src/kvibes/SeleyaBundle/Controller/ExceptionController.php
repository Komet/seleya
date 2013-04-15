<?php

namespace kvibes\SeleyaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseExceptionController;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController extends Controller
{
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null, $format = 'html')
    {
        $kernel = $this->container->get('kernel');
        if ($kernel->getEnvironment() == 'prod') {
            $request = $this->container->get('request');
            $request->setRequestFormat($format);

            $templating = $this->container->get('templating');
            $code = $exception->getStatusCode();

            if (substr($request->getPathInfo(), 0, 6) == '/admin') {
                $template = new TemplateReference('SeleyaBundle', 'Exception', 'error_admin', $format, 'twig');
            } else {
                $template = new TemplateReference('SeleyaBundle', 'Exception', 'error', $format, 'twig');
            }
            if ($templating->exists($template)) {
                $response = $templating->renderResponse($template, array(
                        'status_code'   => $code,
                        'message_code'  => 'error_' . $code,
                        'status_text'   => Response::$statusTexts[$code],
                        'requested_url' => $request->getUri(),
                ));

                $response->setStatusCode($code);
                $response->headers->replace($exception->getHeaders());

                return $response;
            }
        }

        $base = new BaseExceptionController($this->container->get('twig'), $this->container->get('kernel')->isDebug());
        return $base->showAction($request, $exception, $logger, $format);        
    }
}
