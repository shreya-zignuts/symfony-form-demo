<?php

namespace App\Controller;

use App\Entity\StudentForm;
use App\Repository\StudentFormRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StudentController extends AbstractController
{
    #[Route('/', name: 'app_student')]
    public function index(StudentFormRepository $studentFormRepository): Response
    {
        return $this->render('student/index.html.twig', [
            'students' => $studentFormRepository->findAll(),
        ]);
    }

    #[Route('/student/new', name: 'app_student_add')]

   public function newAction(Request $request, EntityManagerInterface $entityManager) {  
    $stud = new StudentForm(); 
    $form = $this->createFormBuilder($stud) 
       ->add('name', TextType::class)
       ->add('password', RepeatedType::class, array( 
          'type' => PasswordType::class, 
          'invalid_message' => 'The password fields 
          must match.', 'options' => array('attr' => array('class' => 'password-field')), 
          'required' => true, 'first_options'  => array('label' => 'Password'), 
          'second_options' => array('label' => 'Re-enter'), 
       )) 
       
       ->add('address', TextareaType::class) 
       ->add('joined', DateType::class, array( 
             'widget' => 'choice', 
       )) 
          
       ->add('gender', ChoiceType::class, array( 
          'choices'  => array( 
             'Male' => true, 
             'Female' => false, 
          ), 
       )) 
       
       ->add('email', EmailType::class) 
       ->add('marks', PercentType::class) 
       ->add('sports', CheckboxType::class, array( 
          'label'    => 'Are you interested in sports?', 'required' => false, 
       )) 
       
       ->add('save', SubmitType::class, array('label' => 'Submit')) 
       ->getForm();  

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($stud);
        $entityManager->flush();

        return $this->redirectToRoute('app_student', [], Response::HTTP_SEE_OTHER);
    }

       return $this->render('student/new.html.twig', array( 
          'form' => $form->createView(), 
       )); 
 } 
}
