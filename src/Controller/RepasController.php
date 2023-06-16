<?php

namespace App\Controller;

use App\Entity\RepasEntity;
use App\Form\RepasFormType;
use App\Form\IngredientFormType;
use App\Entity\IngredientEntity;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\RepasEntityRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RepasController extends AbstractController
{
    #[Route('/front', name: 'app_front')]
    public function front(): Response
    {
        return $this->render('base_front.html.twig', []);
    }

    #[Route('/back', name: 'app_back')]
    public function back(): Response
    {
        return $this->render('base_back.html.twig', [
            'controller_name' => 'RepasController',
        ]);
    }

    #[Route('/showRepas', name: 'repas_show')]

    public function showrepas(): Response
    {
        $repas = $this->getDoctrine()->getRepository(RepasEntity::class)->findAll();
        return $this->render('repas/showRep.html.twig', [
            'repas' => $repas,
        ]);
    }

    
    #[Route('/addRepas', name: 'repas_add')]
    public function add(Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine): Response
    {
        # creates a new instance of RepasEntity class, which is the entity for Recipe meals
        $repas = new RepasEntity();
    
        # creates a form based on the RepasFormType form type class, and sets the form data to be $repas object
        $form = $this->createForm(RepasFormType::class, $repas);
    
        # fetches all the ingredients from database by calling the findALl() function using Doctrine
        $ingredients = $this->getDoctrine()->getRepository(IngredientEntity::class)->findAll();
    
        # performs actions if the form has been submitted and validated successfully
        if ($form->isSubmitted() && $form->isValid()) {
            # gets the image file object from submitted form
            $image = $form->get('image')->getData();
    
            # checks whether an image has been uploaded or not, in case the image field is optional.
            if ($image) {
    
                # gets original image file name without extension
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                # generates safe file name using symfony's Slugger service
                $safeFilename = $slugger->slug($originalFilename);
                # creates a unique generated filename using uniqid() function and append original file extension with a "-".
                $newFilename = $safeFilename . '-' . uniqid('', true) . '.' . $image->guessExtension();
                               
                # moves uploaded image stored in the temporary directory to the new location in storage directory.
                try {
                    $image->move(
                        $this->getParameter('repas_directory'), # The first argument specifies the directory where the images are stored.
                        $newFilename # The second specifies the new image file name.
                    );
                } catch (FileException $e) {
                    # catching exception in case something goes wrong during file upload.
                    // ... handle exception if something happens during file upload
                }
                # updates the image property of repas object with the new image filename.
                $repas->setImage($newFilename);
            }
    
            # persisting the created repas object to the Entity Manager (EM)
            $em = $doctrine->getManager(); 
            $em->persist($repas); 
            $em->flush();
    
            # redirects user to the page displaying the newly created recipe meal using a named route.
            return $this->redirectToRoute('repas_show'); 
        }
        # renders an HTML Twig view form template containing a form created using RepasFormType class.
        return $this->render('repas/addRep.html.twig', [
            'form' => $form->createView(),
            'ingredients' => $ingredients,
        ]);
    }
    


    #[Route('/edit/{id}', name: 'repas_edit')]
    public function  editrep(ManagerRegistry $doctrine, $id,  Request  $request): Response
    {   // Récupéreration d'objet depuis l'identifiant :
        $repas = $doctrine
            ->getRepository(RepasEntity::class)
            ->find($id);
        // Créer un formulaire :
        $form = $this->createForm(RepasFormType::class, $repas);
        // Traiter la requête HTTP avec le formulaire créé précédemment :
        $form->handleRequest($request);
        if (
            $form->isSubmitted()
        ) {
            $em = $doctrine->getManager();
            // Sauvegarder les modifications apportées à l'objet :
            $em->flush();
            return $this->redirectToRoute('repas_show');
        }
        // Afficher le formulaire de modification :
        return $this->renderForm(
            "repas/editRep.html.twig",
            ["form" => $form]
        );
    }


    #[Route("/delete/{id}", name: "repas_delete")]
    public function delete(ManagerRegistry $doctrine, $id, RepasEntityRepository $repository)
    {
        // Récupéreration d'objet depuis l'identifiant :
        $repas = $repository->find($id);
        // Récupéreration l'instance depuis l'objet ManagerRegistry
        $em = $doctrine->getManager();
        // Supprimer l'objet $repas de la base de données :
        $em->remove($repas);
        $em->flush();

        return $this->redirectToRoute('repas_show');
    }

    /************* Calendrier ***************/

    /**
 * @Route("/calendar", name="calendar")
 */
public function calendar(RepasEntityRepository $repasRepository): Response
{
    // Get all meals scheduled
    $repas = $repasRepository->findAll();

    // Create an array of all days of the week
    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    // Create an array to store the meals scheduled for each day and time
    $scheduledRepas = [];

    // Loop through each day of the week
    foreach ($daysOfWeek as $day) {
        // Create an array to store the meals scheduled for this day
        $RepasForDay = [];

        // Loop through each hour of the day (in this example, we're assuming meals are scheduled in hourly increments from 8am to 8pm)
        for ($hour = 8; $hour <= 20; $hour++) {
            // Format the time as a string (e.g. "8:00 AM")
            $time = date('g:i A', strtotime("$hour:00"));

            // Find the meals scheduled for this day and time
            $repasForTime = array_filter($repas, function($repas) use ($day, $hour) {
                return $repas->getDayOfWeek() === $day && $repas->getScheduledTime() === $hour;
            });

            // Add the meals to the array for this day and time
            $repasForDay[$time] = $repasForTime;
        }

        // Add the meals for this day to the overall array
        $scheduledRepas[$day] = $repasForDay;
    }

    return $this->render('calendar/calendar.html.twig', [
        'daysOfWeek' => $daysOfWeek,
        'scheduledrepas' => $scheduledRepas,
    ]);
}

}


    
