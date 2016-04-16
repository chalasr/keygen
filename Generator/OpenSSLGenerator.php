<?php

/*
 * This file is part of the RCH package.
 *
 * (c) Robin Chalas <robin.chalas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RCH\Keygen\Generator;

/**
 * Generate keys for OpenSSL.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class OpenSSLGenerator extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $privateKey = openssl_pkey_new(array(
            'private_key_bits'            => $this->privateKeyBits,
            'private_key_type'            => $this->privateKeyType == 'RSA' ? OPENSSL_KEYTYPE_RSA : OPENSSL_KEYTYPE_DSA,
            'encrypt_key'                 => true,
            'encrypt_key_cipher'          => OPENSSL_CIPHER_AES_256_CBC,
        ));

        $detail = openssl_pkey_get_details($privateKey);

        return [
            'private' => $privateKey,
            'public'  => $detail['key'],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException If an error occurs
     */
    public function export(array $keys)
    {
        try {
            openssl_pkey_export_to_file($keys['private'], $this->privateKeyPath, $this->passphrase);
            file_put_contents($this->publicKeyPath, $keys['public']);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
