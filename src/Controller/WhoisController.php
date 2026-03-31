<?php

namespace App\Controller;

use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/domains')]
final class WhoisController extends AbstractController
{
    #[Route('/whois/{domain}', name: 'domain_whois', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(string $domain, ValidatorInterface $validator): array
    {
        $constraints = new Assert\Collection([
            'domain' => [
                new Assert\NotBlank(),
                new Assert\Hostname(message: 'The server name must be a valid hostname.', requireTld: true),
                new Assert\Length(max: 253, maxMessage: 'The server name must be at most {{ limit }} characters')
            ]
        ]);

        $errors = $validator->validate(['domain' => $domain], $constraints);

        if (count($errors)) {
            throw new HttpException(200, $errors[0]->getMessage());
        }

        try {
            $whois = Factory::get()->createWhois();

            if ($whois->isDomainAvailable($domain)) {
                return [
                    'domain' => $domain,
                    'available' => false
                ];
            }

            $info = $whois->loadDomainInfo($domain);
            $extra = $info->getExtra()['groups'][0];

            return [
                'domain' => $domain,
                'available' => true,
                'registrar' => $info->registrar,
                'registrar_url' => $extra['Registrar URL'],
                'registrant_email' => $extra['Registrant Email'],
                'registrar_abuse' => $extra['Registrar Abuse Contact Email'],
                'name_servers' => $info->nameServers,
                'dnssec' => $info->dnssec,
                'whois_server' => $info->whoisServer,
                'cr_date' => date('Y-m-d H:i:s', $info->creationDate),
                'updated_date' => date('Y-m-d H:i:s', $info->updatedDate),
                'exp_date' => date('Y-m-d H:i:s', $info->expirationDate),
                'owner' => $info->owner,
                'states' => $info->states
            ];
        } catch (ConnectionException $e) {
            throw new HttpException(200, 'Disconnect or connection timeout');
        } catch (ServerMismatchException $e) {
            throw new HttpException(200, 'LD server not found in current server hosts');
        } catch (WhoisException $e) {
            throw new HttpException(200, "Whois server responded with error '{$e->getMessage()}'");
        }
    }
}
