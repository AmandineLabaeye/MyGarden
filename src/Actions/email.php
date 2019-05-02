<?php


namespace App\Actions;


use Symfony\Component\HttpFoundation\Request;

class email
{
    private $templating;

    public function __construct(\Twig_Environment $templating)
    {
        $this->templating = $templating;
    }

    public function sendmail(Request $request, \Swift_Mailer $mailer)
    {
        $Objet = $request->$request->get('Objet');
        $Nom = $request->request->get('Nom');
        $Email = $request->request->get('Emai1');
        $Message = $request->request->get('Message');

        $message = (new \Swift_Message($Objet))
            ->setFrom([$Email => $Nom])
            ->setTo(['amandine.labaeye@gmail.com' => 'Amandine'])
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'email/email.html.twig',
                    ['message' => nl2br($Message)]
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    'emails/email.txt.twig',
                    ['message' => $Message]
                ),
                'text/plain'
            );

        //TODO : if mailer->send($msg) => return true else return false

        $mailer->send($Message);

        // TODO : Non , on ne genere pas de vue pour le second mail, éventuellement retourner true ou false pour utilisation en interne
        return $this->render('msg_mail_site.html.twig', [
            'message' => $message
        ]);
    }

    //todo : send email

    public function accuseMail(Request $request, \Swift_Mailer $mailer)
    {
        $Objet = $request->$request->get('Accusé de reception');
        $Nom = $request->request->get('Nom');
        $Email = $request->request->get('Emai1');
        $Message = $request->request->get('Message');

        $message = (new \Swift_Message($Objet))
            ->setFrom(['noreply@mygarden.fr' => 'Amandine'])
            ->setTo([$Email => $Nom])
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'email/email.html.twig',
                    ['message' => nl2br($Message)]
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    'emails/email.txt.twig',
                    ['message' => $Message]
                ),
                'text/plain'
            );
        $mailer->send($Message);

        // TODO : Non , on ne genere pas de vue pour le second mail, éventuellement retourner true ou false pour utilisation en interne
        return $this->render('msg_mail_site.html.twig', [
            'message' => $Message
        ]);
    }


}