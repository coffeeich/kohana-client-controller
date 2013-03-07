<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Kohana_Client {

    const CLIENT_PAGE_TYPE_DEVELOPMENT = 'development';
    const CLIENT_PAGE_TYPE_PRODUCTION  = 'production';

    const CLIENT_PAGE_THEME_DEVELOPMENT = 'less';
    const CLIENT_PAGE_THEME_PRODUCTION  = 'css';

    static private $request_cache = array();

    /**
     * Статический метод для подключения контроллеров страниц в шаблоне макетов.
     * Устанавливает нужный js-контроллер для страницы в зависимости от настроек в config'е client
     * для текущего контроллера и экшена
     * @see Client_Twig_Extension
     * @example При использовании Kohana View нужно вставить перед закрывающим тэгом body:
     *  <?= Client::inject_controller_script() ?>
     *
     * @example При использовании Twig нужно вставить перед закрывающим тэгом body:
     *  {{ inject_controller_script() }}
     */
    static public function inject_controller_script() {
        $data = self::get_client_page_data();

        if (empty($data)) {
            return;
        }

        $config = Kohana::$config->load('client');

        $page = array_key_exists('page', $data) ? $data['page'] : null;

        if (is_callable($page)) {
            $page = call_user_func($page);
        }

        if (! $page) {
            return;
        }

        $isDevelopment = self::is_development_controller_script();

        $location = "/{$config->get($isDevelopment ? self::CLIENT_PAGE_TYPE_DEVELOPMENT : self::CLIENT_PAGE_TYPE_PRODUCTION)}";

        if ($isDevelopment) {
            $loader = "/{$config->get('loader')}";
            // упрощённое определение пути относительного к папке, в которой расположен loader
            $location = join('/', array_diff(explode('/', ($location)), explode('/', ($loader))));

            return HTML::script("{$config->get('require')}.js", array(
                'data-main' => $loader,
                'data-page' => "{$location}/{$page}",
            ));
        }

        // в папке с билдами ищем сборку для запрашиваемой страницы
        $builds = array_filter(glob(getcwd() . "{$location}/{$page}.*.js"), function($path) use ($page) {
            return preg_replace('/\.\d+$/', '', basename($path, '.js')) === $page;
        });

        // и если она найдена, осуществляем подмену
        if (! empty($builds)) {
            // берем последнюю, так как они уже отсортированы
            $page = basename(array_pop($builds), '.js');
        }

        return HTML::script("{$location}/{$page}.js", array());
    }

    /**
     * Статический метод для подключения стилей страниц в шаблоне макетов.
     * Устанавливает нужный набор css/less тэгов для страницы в зависимости от настроек в config'е client
     * для текущего контроллера и экшена
     * @see Client_Twig_Extension
     * @example При использовании Kohana View нужно вставить перед закрывающим тэгом head:
     *  <?= Client::inject_theme_styles() ?>
     *
     * @example При использовании Twig нужно вставить перед закрывающим тэгом head:
     *  {{ inject_theme_styles() }}
     */
    static public function inject_theme_styles() {
        $data = self::get_client_page_data();

        if (empty($data)) {
            return;
        }

        $config = Kohana::$config->load('client');

        $theme = array_key_exists('theme', $data) ? $data['theme'] : null;

        if (is_callable($theme)) {
            $theme = call_user_func($theme);
        }

        if (empty($theme)) {
            return;
        }

        $page = array_key_exists('page', $data)  ? $data['page']  : null;

        if (is_callable($page)) {
            $page = call_user_func($page);
        }

        if (! $page) {
            return;
        }

        if (! empty($theme)) {
            $theme = "skins/%s/%s/$theme";
        }

        $isDevelopment = self::is_development_controller_script();

        $scripts = "/{$config->get($isDevelopment ? self::CLIENT_PAGE_TYPE_DEVELOPMENT : self::CLIENT_PAGE_TYPE_PRODUCTION)}";

        $ext = $isDevelopment ? self::CLIENT_PAGE_THEME_DEVELOPMENT: self::CLIENT_PAGE_THEME_PRODUCTION;

        if (! $isDevelopment) {
            $builds = array_filter(glob(getcwd() . $scripts . '/' . sprintf($theme, $page . '.*', $ext) . '.' . $ext), function($path) use ($page) {
                return preg_replace('/\.\d+$/', '', basename(dirname(dirname($path)))) === $page;
            });

            // и если она найдена осуществляем подмену
            if (! empty($builds)) {
                // берем последнюю, так как они уже отсортированы
                $page = basename(dirname(dirname(array_pop($builds))));
            }
        }

        $theme = $scripts . '/' . sprintf($theme, $page, $ext) . '.' . $ext;

        $rel = 'stylesheet';

        if ($isDevelopment) {
            $rel .= '/' . $ext;
        }

        return HTML::style($theme, array( 'rel' => $rel ));
    }

    /**
     * Проверка режима работы скрипта на странице. Если в файле конфигурации
     * сказано использовать билд, то вернет false, во всех остальных случаях - true.
     * Это будет означать сто режим работы - разработка.
     * @return bool
     */
    static private function is_development_controller_script() {
        $pageData = self::get_client_page_data();

        return ! (array_key_exists('use_build', $pageData) && $pageData['use_build']);
    }

    /**
     * Получение данных с настройками страницы из файла конфигурации.
     * @return array
     */
    static private function get_client_page_data() {
        if (! array_key_exists(__METHOD__, self::$request_cache)) {
            $pages = Kohana::$config->load('client')->get('pages');

            if (! is_array($pages) || empty($pages)) {
                self::$request_cache[__METHOD__] = array();
            } else {
                list($directory, $controller, $action) = array(
                    Request::$current->directory(),
                    Request::$current->controller(),
                    Request::$current->action()
                );

                if (strlen($directory) !== 0) {
                    $controller = $directory . '_' . $controller;
                }

                self::$request_cache[__METHOD__] = isset($pages[$controller][$action])
                    ? $pages[$controller][$action]
                    : array();
            }
        }

        return self::$request_cache[__METHOD__];
    }

}
