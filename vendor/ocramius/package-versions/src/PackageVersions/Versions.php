<?php

declare(strict_types=1);

namespace PackageVersions;

/**
 * This class is generated by ocramius/package-versions, specifically by
 * @see \PackageVersions\Installer
 *
 * This file is overwritten at every run of `composer install` or `composer update`.
 */
final class Versions
{
    public const ROOT_PACKAGE_NAME = 'shopware/shopware';
    public const VERSIONS          = array (
  'aws/aws-sdk-php' => '3.120.0@289d716c7a418fc30a530ca83107e738f8dd1ebc',
  'bcremer/line-reader' => '0.2.0@b2bdbcf97df9a05b8ce317c3a5382610cd832419',
  'beberlei/assert' => 'v2.9.9@124317de301b7c91d5fce34c98bba2c6925bec95',
  'beberlei/doctrineextensions' => 'v1.2.1@a6b55e257ebaabd66f3547a962037fc4eb645378',
  'clue/stream-filter' => 'v1.4.1@5a58cc30a8bd6a4eb8f856adf61dd3e013f53f71',
  'cocur/slugify' => 'v3.1@b2ccf7b735f4f3df3979aef2e1ebf8e19ca772f7',
  'doctrine/annotations' => 'v1.8.0@904dca4eb10715b92569fbcd79e201d5c349b6bc',
  'doctrine/cache' => 'v1.8.0@d768d58baee9a4862ca783840eca1b9add7a7f57',
  'doctrine/collections' => 'v1.6.2@c5e0bc17b1620e97c968ac409acbff28b8b850be',
  'doctrine/common' => 'v2.11.0@b8ca1dcf6b0dc8a2af7a09baac8d0c48345df4ff',
  'doctrine/dbal' => 'v2.6.3@e3eed9b1facbb0ced3a0995244843a189e7d1b13',
  'doctrine/event-manager' => '1.1.0@629572819973f13486371cb611386eb17851e85c',
  'doctrine/inflector' => '1.3.1@ec3a55242203ffa6a4b27c58176da97ff0a7aec1',
  'doctrine/instantiator' => '1.3.0@ae466f726242e637cebdd526a7d991b9433bacf1',
  'doctrine/lexer' => '1.2.0@5242d66dbeb21a30dd8a3e66bf7a73b66e05e1f6',
  'doctrine/orm' => 'v2.6.4@b52ef5a1002f99ab506a5a2d6dba5a2c236c5f43',
  'doctrine/persistence' => '1.2.0@43526ae63312942e5316100bb3ed589ba1aba491',
  'doctrine/reflection' => 'v1.0.0@02538d3f95e88eb397a5f86274deb2c6175c2ab6',
  'egulias/email-validator' => '1.2.14@5642614492f0ca2064c01d60cc33284cc2f731a9',
  'elasticsearch/elasticsearch' => 'v6.7.1@7be453dd36d1b141b779f2cb956715f8e04ac2f4',
  'fig/link-util' => '1.0.0@1a07821801a148be4add11ab0603e4af55a72fac',
  'firebase/php-jwt' => 'v5.0.0@9984a4d3a32ae7673d6971ea00bae9d0a1abba0e',
  'google/auth' => 'v1.6.1@45635ac69d0b95f38885531d4ebcdfcb2ebb6f36',
  'google/cloud' => 'v0.49.0@c2664e6056f67dfba74edc8dc777ed5cd882b715',
  'google/gax' => '0.29.1@c473e086f1cdf44c2c482be079133a31cbe54f14',
  'google/proto-client' => '0.29.0@47e52e5819426edff97c2831efe022b0141c44f1',
  'google/protobuf' => 'v3.5.2@801d776b4e4c9fce294d800974266c616bb2ce40',
  'grpc/grpc' => '1.25.0@bdb165d6712db0cfcc35edae2d14fb2b00cabc88',
  'guzzlehttp/guzzle' => '5.3.3@93bbdb30d59be6cd9839495306c65f2907370eb9',
  'guzzlehttp/promises' => 'v1.3.1@a59da6cf61d80060647ff4d3eb2c03a2bc694646',
  'guzzlehttp/psr7' => '1.4.2@f5b8a8512e2b58b0071a7280e39f14f72e05d87c',
  'guzzlehttp/ringphp' => '1.1.1@5e2a174052995663dd68e6b5ad838afd47dd615b',
  'guzzlehttp/streams' => '3.0.0@47aaa48e27dae43d39fc1cea0ccf0d84ac1a2ba5',
  'league/flysystem' => '1.0.46@f3e0d925c18b92cf3ce84ea5cc58d62a1762a2b2',
  'league/flysystem-aws-s3-v3' => '1.0.19@f135691ef6761542af301b7c9880f140fb12dc74',
  'monolog/monolog' => '1.23.0@fd8c787753b3a2ad11bc60c063cff1358a32a3b4',
  'mpdf/mpdf' => 'v7.1.9@a0fc1215d2306aa3b4ba6e97bd6ebe4bab6a88fb',
  'mtdowling/jmespath.php' => '2.4.0@adcc9531682cf87dfda21e1fd5d0e7a41d292fac',
  'myclabs/deep-copy' => '1.9.3@007c053ae6f31bba39dfa19a7726f56e9763bbea',
  'ocramius/package-versions' => '1.4.2@44af6f3a2e2e04f2af46bcb302ad9600cba41c7d',
  'ocramius/proxy-manager' => '2.2.2@14b137b06b0f911944132df9d51e445a35920ab1',
  'ongr/elasticsearch-dsl' => 'v6.0.3@56e59e51cf066f5ee4dff2612b3831b5a7792e67',
  'oyejorge/less.php' => 'v1.7.0.14@42925c5a01a07d67ca7e82dfc8fb31814d557bc9',
  'paragonie/random_compat' => 'v2.0.18@0a58ef6e3146256cc3dc7cc393927bcc7d1b72db',
  'php-http/curl-client' => 'v1.7.1@6341a93d00e5d953fc868a3928b5167e6513f2b6',
  'php-http/discovery' => '1.7.0@e822f86a6983790aa17ab13aa7e69631e86806b6',
  'php-http/httplug' => 'v1.1.0@1c6381726c18579c4ca2ef1ec1498fdae8bdf018',
  'php-http/message' => '1.6.0@2edd63bae5f52f79363c5f18904b05ce3a4b7253',
  'php-http/message-factory' => 'v1.0.2@a478cb11f66a6ac48d8954216cfed9aa06a501a1',
  'php-http/promise' => 'v1.0.0@dc494cdc9d7160b9a09bd5573272195242ce7980',
  'phpstan/phpstan-shim' => '0.11.19@e3c06b1d63691dae644ae1e5b540905c8c021801',
  'psr/cache' => '1.0.1@d11b50ad223250cf17b86e38383413f5a6764bf8',
  'psr/container' => '1.0.0@b7ce3b176482dbbc1245ebf52b181af44c2cf55f',
  'psr/http-message' => '1.0.1@f6561bf28d520154e4b0ec72be95418abe6d9363',
  'psr/link' => '1.0.0@eea8e8662d5cd3ae4517c9b864493f59fca95562',
  'psr/log' => '1.1.2@446d54b4cb6bf489fc9d75f55843658e6f25d801',
  'ramsey/uuid' => '3.5.2@5677cfe02397dd6b58c861870dfaa5d9007d3954',
  'react/promise' => 'v2.7.1@31ffa96f8d2ed0341a57848cbb84d88b89dd664d',
  'rize/uri-template' => '0.3.2@9e5fdd5c47147aa5adf7f760002ee591ed37b9ca',
  'setasign/fpdf' => '1.8.1@2c68c9e6c034ac3187d25968790139a73184cdb1',
  'setasign/fpdi' => '1.6.2@a6ad58897a6d97cc2d2cd2adaeda343b25a368ea',
  'stecman/symfony-console-completion' => '0.10.1@7bfa9b93e216896419f2f8de659935d7e04fecd8',
  'superbalist/flysystem-google-storage' => '6.0.0@59aac00907703132e2db2ba0bb81b1ff46f660e1',
  'symfony/cache' => 'v4.3.8@83dca34362a0aba2b93aa1226dac6ef7cfea1262',
  'symfony/cache-contracts' => 'v1.1.7@af50d14ada9e4e82cfabfabdc502d144f89be0a1',
  'symfony/class-loader' => 'v3.4.35@e212b06996819a2bce026a63da03b7182d05a690',
  'symfony/config' => 'v3.4.35@c3a30587de97263d2813a3c81b74126c58b67a4f',
  'symfony/console' => 'v3.4.35@17b154f932c5874cdbda6d05796b6490eec9f9f7',
  'symfony/debug' => 'v4.4.0@b24b791f817116b29e52a63e8544884cf9a40757',
  'symfony/dependency-injection' => 'v3.4.35@0ea4d39ca82409a25a43b61ce828048a90000920',
  'symfony/event-dispatcher' => 'v4.4.0@ab1c43e17fff802bef0a898f3bc088ac33b8e0e1',
  'symfony/event-dispatcher-contracts' => 'v1.1.7@c43ab685673fb6c8d84220c77897b1d6cdbe1d18',
  'symfony/expression-language' => 'v3.4.35@434b23d0deaf6a9735f36036aac484bf423a2bae',
  'symfony/filesystem' => 'v3.4.35@00e3a6ddd723b8bcfe4f2a1b6f82b98eeeb51516',
  'symfony/finder' => 'v3.4.35@3e915e5ce305f8bc8017597f71f1f4095092ddf8',
  'symfony/form' => 'v3.4.35@13dbd70e89370f8d7d697fdc23bd8132f281b166',
  'symfony/http-foundation' => 'v3.4.35@9e4b3ac8fa3348b4811674d23de32d201de225ce',
  'symfony/http-kernel' => 'v3.4.35@e1764b3de00ec5636dd03d02fd44bcb1147d70d9',
  'symfony/inflector' => 'v4.4.0@98581481d9ddabe4db3a66e10202fe1fa08d791b',
  'symfony/intl' => 'v4.4.0@299cbfd6be791438e8d93ffb25765b5b93021bf9',
  'symfony/options-resolver' => 'v3.4.35@b224d20be60e6f7b55cd66914379a13a0b28651a',
  'symfony/polyfill-ctype' => 'v1.12.0@550ebaac289296ce228a706d0867afc34687e3f4',
  'symfony/polyfill-intl-icu' => 'v1.12.0@66810b9d6eb4af54d543867909d65ab9af654d7e',
  'symfony/polyfill-mbstring' => 'v1.12.0@b42a2f66e8f1b15ccf25652c3424265923eb4f17',
  'symfony/polyfill-php56' => 'v1.12.0@0e3b212e96a51338639d8ce175c046d7729c3403',
  'symfony/polyfill-php70' => 'v1.12.0@54b4c428a0054e254223797d2713c31e08610831',
  'symfony/polyfill-util' => 'v1.12.0@4317de1386717b4c22caed7725350a8887ab205c',
  'symfony/process' => 'v3.4.35@c19da50bc3e8fa7d60628fdb4ab5d67de534cf3e',
  'symfony/property-access' => 'v4.4.0@4df120cbe473d850eb59f75c341915955e45f25b',
  'symfony/serializer' => 'v3.4.35@9d14f7ff2c585a8a9f6f980253066285ddc2f675',
  'symfony/service-contracts' => 'v1.1.8@ffc7f5692092df31515df2a5ecf3b7302b3ddacf',
  'symfony/translation' => 'v3.4.35@2031c895bc97ac1787d418d90bd1ed7d299f2772',
  'symfony/validator' => 'v3.4.35@b11f45742c5c9a228cedc46b70c6317780a1ac80',
  'symfony/var-exporter' => 'v4.4.0@72feb69a33def8f761e612360588e40bac98caad',
  'symfony/web-link' => 'v3.4.35@fbe342e109c2ca60c39c6595a8e98aed1138937d',
  'zendframework/zend-code' => '3.4.0@46feaeecea14161734b56c1ace74f28cb329f194',
  'zendframework/zend-escaper' => '2.5.2@2dcd14b61a72d8b8e27d579c6344e12c26141d4e',
  'zendframework/zend-eventmanager' => '3.2.1@a5e2583a211f73604691586b8406ff7296a946dd',
  'behat/behat' => 'v3.5.0@e4bce688be0c2029dc1700e46058d86428c63cab',
  'behat/gherkin' => 'v4.6.0@ab0a02ea14893860bca00f225f5621d351a3ad07',
  'behat/mink' => 'v1.7.1@e6930b9c74693dff7f4e58577e1b1743399f3ff9',
  'behat/mink-browserkit-driver' => '1.3.3@1b9a7ce903cfdaaec5fb32bfdbb26118343662eb',
  'behat/mink-extension' => 'v2.2@5b4bda64ff456104564317e212c823e45cad9d59',
  'behat/mink-goutte-driver' => 'v1.2.1@8b9ad6d2d95bc70b840d15323365f52fcdaea6ca',
  'behat/mink-selenium2-driver' => 'v1.3.1@473a9f3ebe0c134ee1e623ce8a9c852832020288',
  'behat/transliterator' => 'v1.2.0@826ce7e9c2a6664c0d1f381cbb38b1fb80a7ee2c',
  'composer/semver' => '1.5.0@46d9139568ccb8d9e7cdd4539cab7347568a5e2e',
  'composer/xdebug-handler' => '1.4.0@cbe23383749496fe0f373345208b79568e4bc248',
  'container-interop/container-interop' => '1.2.0@79cbf1341c22ec75643d841642dd5d6acd83bdb8',
  'fabpot/goutte' => 'v2.0.4@0ad3ee6dc2d0aaa832a80041a1e09bf394e99802',
  'friendsofphp/php-cs-fixer' => 'v2.16.0@ceaff36bee1ed3f1bbbedca36d2528c0826c336d',
  'instaclick/php-webdriver' => '1.4.6@bd9405077ca04129a73059a06873bedb5e138402',
  'jakub-onderka/php-var-dump-check' => 'v0.3@c7b30cbe73b7815811d079cb5bc326c313dd084e',
  'kubawerlos/php-cs-fixer-custom-fixers' => 'v1.16.2@2443b83696b0bb0435a1aa75ed0185515400411d',
  'phar-io/manifest' => '1.0.3@7761fcacf03b4d4f16e7ccb606d4879ca431fcf4',
  'phar-io/version' => '2.0.1@45a2ec53a73c70ce41d55cedef9063630abaf1b6',
  'php-cs-fixer/diff' => 'v1.3.0@78bb099e9c16361126c86ce82ec4405ebab8e756',
  'phpdocumentor/reflection-common' => '2.0.0@63a995caa1ca9e5590304cd845c15ad6d482a62a',
  'phpdocumentor/reflection-docblock' => '4.3.2@b83ff7cfcfee7827e1e78b637a5904fe6a96698e',
  'phpdocumentor/type-resolver' => '1.0.1@2e32a6d48972b2c1976ed5d8967145b6cec4a4a9',
  'phpspec/prophecy' => '1.9.0@f6811d96d97bdf400077a0cc100ae56aa32b9203',
  'phpunit/php-code-coverage' => '6.1.4@807e6013b00af69b6c5d9ceb4282d0393dbb9d8d',
  'phpunit/php-file-iterator' => '2.0.2@050bedf145a257b1ff02746c31894800e5122946',
  'phpunit/php-text-template' => '1.2.1@31f8b717e51d9a2afca6c9f046f5d69fc27c8686',
  'phpunit/php-timer' => '2.1.2@1038454804406b0b5f5f520358e78c1c2f71501e',
  'phpunit/php-token-stream' => '3.1.1@995192df77f63a59e47f025390d2d1fdf8f425ff',
  'phpunit/phpunit' => '7.5.17@4c92a15296e58191a4cd74cff3b34fc8e374174a',
  'sebastian/code-unit-reverse-lookup' => '1.0.1@4419fcdb5eabb9caa61a27c7a1db532a6b55dd18',
  'sebastian/comparator' => '3.0.2@5de4fc177adf9bce8df98d8d141a7559d7ccf6da',
  'sebastian/diff' => '3.0.2@720fcc7e9b5cf384ea68d9d930d480907a0c1a29',
  'sebastian/environment' => '4.2.3@464c90d7bdf5ad4e8a6aea15c091fec0603d4368',
  'sebastian/exporter' => '3.1.2@68609e1261d215ea5b21b7987539cbfbe156ec3e',
  'sebastian/global-state' => '2.0.0@e8ba02eed7bbbb9e59e43dedd3dddeff4a56b0c4',
  'sebastian/object-enumerator' => '3.0.3@7cfd9e65d11ffb5af41198476395774d4c8a84c5',
  'sebastian/object-reflector' => '1.1.1@773f97c67f28de00d397be301821b06708fca0be',
  'sebastian/recursion-context' => '3.0.0@5b0cd723502bac3b006cbf3dbf7a1e3fcefe4fa8',
  'sebastian/resource-operations' => '2.0.1@4d7a795d35b889bf80a0cc04e08d77cedfa917a9',
  'sebastian/version' => '2.0.1@99732be0ddb3361e16ad77b68ba41efc8e979019',
  'sensiolabs/behat-page-object-extension' => 'v2.1.0@bd2a34221ba65ea8c86d8e693992d718de03dbae',
  'symfony/browser-kit' => 'v2.8.52@b507697225f32a76a9d333d0766fb46353e9d00d',
  'symfony/css-selector' => 'v2.8.52@7b1692e418d7ccac24c373528453bc90e42797de',
  'symfony/dom-crawler' => 'v2.8.52@2cdc7d3909eea6f982a6298d2e9ab7db01b6403c',
  'symfony/polyfill-php72' => 'v1.12.0@04ce3335667451138df4307d6a9b61565560199e',
  'symfony/stopwatch' => 'v4.4.0@5745b514fc56ae1907c6b8ed74f94f90f64694e9',
  'symfony/yaml' => 'v3.4.22@ba11776e9e6c15ad5759a07bffb15899bac75c2d',
  'theseer/tokenizer' => '1.1.3@11336f6f84e16a720dae9d8e6ed5019efa85a0f9',
  'webmozart/assert' => '1.5.0@88e6d84706d09a236046d686bbea96f07b3a34f4',
  'shopware/shopware' => 'dev-version/5.6.0@49fc9732ca0a2f7c955ea19a438bf3c5374233b0',
);

    private function __construct()
    {
    }

    /**
     * @throws \OutOfBoundsException If a version cannot be located.
     */
    public static function getVersion(string $packageName) : string
    {
        if (isset(self::VERSIONS[$packageName])) {
            return self::VERSIONS[$packageName];
        }

        throw new \OutOfBoundsException(
            'Required package "' . $packageName . '" is not installed: check your ./vendor/composer/installed.json and/or ./composer.lock files'
        );
    }
}
