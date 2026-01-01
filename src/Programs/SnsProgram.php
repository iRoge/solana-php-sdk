<?php

namespace Iroge\SolanaPhpSdk\Programs;


use Iroge\SolanaPhpSdk\Exceptions\InputValidationException;
use Iroge\SolanaPhpSdk\Program;
use Iroge\SolanaPhpSdk\Programs\SNS\Bindings;
use Iroge\SolanaPhpSdk\Programs\SNS\Utils;
use Iroge\SolanaPhpSdk\Programs\SNS\Instructions\Instructions;
use Iroge\SolanaPhpSdk\PublicKey;
use Iroge\SolanaPhpSdk\SolanaRpcClient;



class SnsProgram extends Program
{

    use Instructions;
    use Utils;
    use Bindings;

    public mixed $config;
    public PublicKey $centralStateSNSRecords;

     public  const SYSVAR_RENT_PUBKEY = 'SysvarRent111111111111111111111111111111111';

    /**
     * @throws InputValidationException
     */
    public function __construct(SolanaRpcClient $client, $config = null)
    {
        parent::__construct($client);
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = $this->loadConstants();
        }
        $sns_records_id = new PublicKey($this->config['BONFIDA_SNS_RECORDS_ID']);

        $this->centralStateSNSRecords = PublicKey::findProgramAddressSync(
            [$sns_records_id],
            $sns_records_id)[0];

        return $this;
    }


}
