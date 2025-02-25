<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Rating;
use App\Form\CompanyprofileType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;



    /**
     * @Route("/company", name="company_")
     */
class CompanyController extends AbstractController
{
    /**
     * @Route("/profile", name="companyprofile")
     */

    public function AddEditCompany(Request $request, FileUploader $fileUploader)
    {
        $em= $this->getDoctrine()->getManager();
        $loggedUser = $this->getUser();

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->redirectToRoute('security_login');}
        $company = $em->getRepository(Company::class)->findOneByUserId($loggedUser);

        if ($company and !$company->getStatus()){
            return $this->render('company/pending-company-request.html.twig');
        }
        else{
            !$company && $company = new Company();
            $form=$this->createForm(CompanyprofileType::class, $company);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                /**
                 * @var UploadedFile $companyimage
                 */
                $companyimage = $form->get('companyImageName')->getData();
                if ($companyimage) {
                    $newFilename = $fileUploader->upload($companyimage);
                    $company->setCompanyImageName($newFilename);
                }
                $company->setStatus(false);
                !$company->getUserId() && $company->setUserId($loggedUser);
                $em->persist($company);
                $em->flush();

            }
    }
        return $this->render('company/companyprofile.html.twig', [
        'form'=>$form->createView(),
            'company' => $company

    ]);
    }
    /**
     * @Route("/companies", name="companies_list")
     */

    public function ShowCompanies(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->findAll();
        foreach ($company as $comp){
            $reviews = $em->getRepository(Rating::class)->findByCompany($comp);
            $stars = 0;
            $nbrRate = count($reviews);
            foreach ($reviews as $rev){
                $stars = $stars + $rev->getStars();
            }
            if ($nbrRate == 0) {
                $stars = 0;
            } else {
                $stars = $stars / $nbrRate;
            }
            $comp->setStars($stars);
            $em->persist($comp);
            $em->flush();
        }
        $pagination = $paginator->paginate($company, $request->query->getInt('page', 1), 5);
        return $this->render('company/companiesList.html.twig', [
            'companies_list' => $pagination,
        ]);
    }
    /**
     * @Route("/findCompanies", name="Companies_filter")
     * @param Request $request
     */

    public function findCompanies(Request $request, PaginatorInterface $paginator){

        $name = $request->get('name');
        $country = $request->get('country');
        $adresse = $request->get('adresse');

        $filtredUsers = $this->findCompaniesQuery($name,$country,$adresse);

        $pagination = $paginator->paginate(
            $filtredUsers, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            4 /*limit per page*/
        );

        return $this->render('company/companiesList.html.twig', [
            'companies_list' => $pagination,
        ]);


    }
    public function findCompaniesQuery($name, $country, $adresse)
    {
        $req = $this->getDoctrine()->getManager();
        return $req->createQuery(" select v from App:Company v where v.country like :c and v.companyName like :n and v.companyAdress like :a")

            ->setParameter('n', $name ? '%'.$name.'%' : '%%')
            ->setParameter('c', $country ? '%'.$country.'%' : '%%')
            ->setParameter('a', $adresse ? '%'.$adresse.'%' : '%%')
            ->getResult();
    }

    /**
     * @Route("/rateCompany/{id}", name="Companies_rating")
     * @param Request $request
     */
    public function createReview(Request $request, $id)
    {

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->redirectToRoute('security_login');}

            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $company = $em->getRepository(Company::class)->find($id);
            $reviews = $em->getRepository(Rating::class)->findByCompany($company);
            $exist = false;

            foreach ($reviews as $rev) {
                if ($rev->getOwner() == $user) {

                    $rev->setTitle(($request->get('title')));
                    $rev->setDescription(($request->get('description')));
                    $rev->setStars($request->get('stars'));
                    $rev->setCompany($company);
                    $rev->setOwner($user);
                    $em->persist($rev);
                    $em->flush();
                    $exist = true;
                }
            }
        if ($request->isMethod('POST')) {
                if (!$exist) {
                    $review = new Rating();
                    $review->setTitle(($request->get('title')));
                    $review->setDescription(($request->get('description')));
                    $review->setStars($request->get('stars'));
                    $review->setCompany($company);
                    $review->setOwner($user);
                    $em->persist($review);
                    $em->flush();
                    return $this->redirectToRoute("company_companies_list");
                } else {

                    return $this->redirectToRoute("company_companies_list");
                }

            }


            return $this->render('company/companiesRating.html.twig',[
                'company'=>$company
                ]);
    }



}
