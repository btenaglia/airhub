<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication() {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__ . '/../../bootstrap/start.php';
    }

    /**
     * Get a new instance of the specified service
     *
     * @param string $servicePrefix The service name
     *
     * @return Service
     */
    protected function getService($servicePrefix) {
        return App::make($servicePrefix . 'Service');
    }

}
