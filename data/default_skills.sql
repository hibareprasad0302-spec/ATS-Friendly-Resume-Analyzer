USE ats_resume_analyzer;

-- Programming Languages
INSERT INTO skills_master (skill_name, category, aliases) VALUES
('php', 'programming', '["php", "php8", "php7"]'),
('python', 'programming', '["python", "python3", "py"]'),
('javascript', 'programming', '["javascript", "js", "ecmascript", "es6", "es2015"]'),
('typescript', 'programming', '["typescript", "ts"]'),
('java', 'programming', '["java", "j2ee", "jdk"]'),
('c++', 'programming', '["c++", "cpp", "cplusplus"]'),
('c#', 'programming', '["c#", "csharp", "c sharp"]'),
('c', 'programming', '["c programming", "ansi c"]'),
('ruby', 'programming', '["ruby", "rb"]'),
('go', 'programming', '["go", "golang"]'),
('rust', 'programming', '["rust", "rustlang"]'),
('swift', 'programming', '["swift", "swiftui"]'),
('kotlin', 'programming', '["kotlin", "kt"]'),
('scala', 'programming', '["scala"]'),
('r', 'programming', '["r programming", "r language", "rlang"]'),
('perl', 'programming', '["perl"]'),
('sql', 'programming', '["sql", "structured query language"]'),
('html', 'programming', '["html", "html5"]'),
('css', 'programming', '["css", "css3", "stylesheet"]'),
('bash', 'programming', '["bash", "shell script", "shell scripting"]'),
('powershell', 'programming', '["powershell", "ps1"]'),
('dart', 'programming', '["dart"]'),
('lua', 'programming', '["lua"]'),
('matlab', 'programming', '["matlab"]'),
('objective-c', 'programming', '["objective-c", "objc", "objective c"]');

-- Frameworks & Libraries
INSERT INTO skills_master (skill_name, category, aliases) VALUES
('react', 'framework', '["react", "reactjs", "react.js"]'),
('angular', 'framework', '["angular", "angularjs", "angular.js"]'),
('vue', 'framework', '["vue", "vuejs", "vue.js"]'),
('nextjs', 'framework', '["nextjs", "next.js", "next"]'),
('nuxtjs', 'framework', '["nuxtjs", "nuxt.js", "nuxt"]'),
('svelte', 'framework', '["svelte", "sveltekit"]'),
('django', 'framework', '["django"]'),
('flask', 'framework', '["flask"]'),
('fastapi', 'framework', '["fastapi", "fast api"]'),
('laravel', 'framework', '["laravel"]'),
('symfony', 'framework', '["symfony"]'),
('codeigniter', 'framework', '["codeigniter", "ci"]'),
('spring', 'framework', '["spring", "spring boot", "springboot"]'),
('express', 'framework', '["express", "expressjs", "express.js"]'),
('rails', 'framework', '["rails", "ruby on rails", "ror"]'),
('dotnet', 'framework', '["dotnet", ".net", "asp.net", "dot net"]'),
('nodejs', 'framework', '["nodejs", "node.js", "node"]'),
('jquery', 'framework', '["jquery"]'),
('bootstrap', 'framework', '["bootstrap"]'),
('tailwindcss', 'framework', '["tailwindcss", "tailwind", "tailwind css"]'),
('flutter', 'framework', '["flutter"]'),
('react native', 'framework', '["react native", "react-native"]'),
('electron', 'framework', '["electron", "electronjs"]'),
('spring boot', 'framework', '["spring boot", "springboot"]'),
('hibernate', 'framework', '["hibernate"]'),
('tensorflow', 'framework', '["tensorflow", "tf"]'),
('pytorch', 'framework', '["pytorch", "torch"]'),
('keras', 'framework', '["keras"]'),
('pandas', 'framework', '["pandas"]'),
('numpy', 'framework', '["numpy"]'),
('scikit-learn', 'framework', '["scikit-learn", "sklearn", "scikit learn"]');

-- Databases
INSERT INTO skills_master (skill_name, category, aliases) VALUES
('mysql', 'database', '["mysql", "my sql"]'),
('postgresql', 'database', '["postgresql", "postgres", "psql", "pgsql"]'),
('mongodb', 'database', '["mongodb", "mongo"]'),
('redis', 'database', '["redis"]'),
('elasticsearch', 'database', '["elasticsearch", "elastic search", "elastic"]'),
('sqlite', 'database', '["sqlite", "sqlite3"]'),
('oracle', 'database', '["oracle", "oracle db", "oracle database"]'),
('sql server', 'database', '["sql server", "mssql", "microsoft sql server"]'),
('dynamodb', 'database', '["dynamodb", "dynamo db", "dynamo"]'),
('cassandra', 'database', '["cassandra", "apache cassandra"]'),
('firebase', 'database', '["firebase", "firestore"]'),
('mariadb', 'database', '["mariadb", "maria db"]'),
('couchdb', 'database', '["couchdb", "couch db"]'),
('neo4j', 'database', '["neo4j", "neo 4j"]');

-- DevOps & Tools
INSERT INTO skills_master (skill_name, category, aliases) VALUES
('git', 'tool', '["git", "github", "gitlab", "bitbucket"]'),
('docker', 'tool', '["docker", "dockerfile", "docker-compose"]'),
('kubernetes', 'tool', '["kubernetes", "k8s", "kube"]'),
('jenkins', 'tool', '["jenkins", "jenkins ci"]'),
('terraform', 'tool', '["terraform", "tf"]'),
('ansible', 'tool', '["ansible"]'),
('webpack', 'tool', '["webpack"]'),
('vite', 'tool', '["vite"]'),
('nginx', 'tool', '["nginx"]'),
('apache', 'tool', '["apache", "apache http", "httpd"]'),
('jira', 'tool', '["jira", "atlassian jira"]'),
('confluence', 'tool', '["confluence"]'),
('postman', 'tool', '["postman"]'),
('swagger', 'tool', '["swagger", "openapi"]'),
('grafana', 'tool', '["grafana"]'),
('prometheus', 'tool', '["prometheus"]'),
('ci/cd', 'tool', '["ci/cd", "ci cd", "continuous integration", "continuous deployment"]'),
('linux', 'tool', '["linux", "unix", "ubuntu", "centos", "debian"]'),
('maven', 'tool', '["maven", "apache maven"]'),
('gradle', 'tool', '["gradle"]'),
('npm', 'tool', '["npm", "node package manager"]'),
('yarn', 'tool', '["yarn"]'),
('pip', 'tool', '["pip"]'),
('composer', 'tool', '["composer"]'),
('vagrant', 'tool', '["vagrant"]'),
('puppet', 'tool', '["puppet"]'),
('chef', 'tool', '["chef"]'),
('selenium', 'tool', '["selenium", "selenium webdriver"]'),
('cypress', 'tool', '["cypress"]'),
('jest', 'tool', '["jest"]'),
('phpunit', 'tool', '["phpunit"]'),
('pytest', 'tool', '["pytest"]'),
('figma', 'tool', '["figma"]'),
('photoshop', 'tool', '["photoshop", "adobe photoshop"]'),
('vs code', 'tool', '["vs code", "visual studio code", "vscode"]');

-- Cloud Platforms
INSERT INTO skills_master (skill_name, category, aliases) VALUES
('aws', 'cloud', '["aws", "amazon web services", "amazon cloud"]'),
('azure', 'cloud', '["azure", "microsoft azure", "ms azure"]'),
('gcp', 'cloud', '["gcp", "google cloud", "google cloud platform"]'),
('heroku', 'cloud', '["heroku"]'),
('digitalocean', 'cloud', '["digitalocean", "digital ocean"]'),
('vercel', 'cloud', '["vercel"]'),
('netlify', 'cloud', '["netlify"]'),
('cloudflare', 'cloud', '["cloudflare", "cloud flare"]'),
('lambda', 'cloud', '["lambda", "aws lambda", "serverless"]'),
('s3', 'cloud', '["s3", "aws s3", "amazon s3"]'),
('ec2', 'cloud', '["ec2", "aws ec2"]'),
('cloudfront', 'cloud', '["cloudfront", "aws cloudfront"]');

-- Data & Analytics
INSERT INTO skills_master (skill_name, category, aliases) VALUES
('machine learning', 'data', '["machine learning", "ml"]'),
('deep learning', 'data', '["deep learning", "dl"]'),
('artificial intelligence', 'data', '["artificial intelligence", "ai"]'),
('data science', 'data', '["data science"]'),
('data analysis', 'data', '["data analysis", "data analytics"]'),
('data engineering', 'data', '["data engineering"]'),
('big data', 'data', '["big data"]'),
('hadoop', 'data', '["hadoop", "apache hadoop"]'),
('spark', 'data', '["spark", "apache spark", "pyspark"]'),
('tableau', 'data', '["tableau"]'),
('power bi', 'data', '["power bi", "powerbi", "microsoft power bi"]'),
('nlp', 'data', '["nlp", "natural language processing"]'),
('computer vision', 'data', '["computer vision", "cv", "image recognition"]'),
('etl', 'data', '["etl", "extract transform load"]');

-- APIs & Protocols
INSERT INTO skills_master (skill_name, category, aliases) VALUES
('rest api', 'api', '["rest api", "restful", "rest", "restful api"]'),
('graphql', 'api', '["graphql", "graph ql"]'),
('grpc', 'api', '["grpc", "g-rpc"]'),
('websocket', 'api', '["websocket", "websockets", "ws"]'),
('oauth', 'api', '["oauth", "oauth2", "oauth 2.0"]'),
('jwt', 'api', '["jwt", "json web token", "json web tokens"]'),
('soap', 'api', '["soap", "soap api"]'),
('microservices', 'api', '["microservices", "micro services"]');

-- Soft Skills
INSERT INTO skills_master (skill_name, category, aliases) VALUES
('communication', 'soft_skill', '["communication", "communication skills"]'),
('teamwork', 'soft_skill', '["teamwork", "team work", "team player", "collaboration"]'),
('leadership', 'soft_skill', '["leadership", "team lead", "team leadership"]'),
('problem solving', 'soft_skill', '["problem solving", "problem-solving", "analytical thinking"]'),
('time management', 'soft_skill', '["time management", "time-management"]'),
('adaptability', 'soft_skill', '["adaptability", "flexible", "flexibility"]'),
('critical thinking', 'soft_skill', '["critical thinking", "critical-thinking"]'),
('project management', 'soft_skill', '["project management", "pm"]'),
('agile', 'soft_skill', '["agile", "agile methodology", "scrum", "kanban"]'),
('mentoring', 'soft_skill', '["mentoring", "coaching", "mentorship"]');

-- Design & Methodologies
INSERT INTO skills_master (skill_name, category, aliases) VALUES
('responsive design', 'design', '["responsive design", "responsive web design", "rwd"]'),
('ux design', 'design', '["ux design", "ux", "user experience"]'),
('ui design', 'design', '["ui design", "ui", "user interface"]'),
('design patterns', 'design', '["design patterns"]'),
('oop', 'design', '["oop", "object oriented programming", "object-oriented"]'),
('mvc', 'design', '["mvc", "model view controller"]'),
('tdd', 'design', '["tdd", "test driven development", "test-driven"]'),
('solid', 'design', '["solid", "solid principles"]'),
('clean architecture', 'design', '["clean architecture"]'),
('ddd', 'design', '["ddd", "domain driven design"]');

-- Security
INSERT INTO skills_master (skill_name, category, aliases) VALUES
('cybersecurity', 'security', '["cybersecurity", "cyber security", "information security"]'),
('penetration testing', 'security', '["penetration testing", "pen testing", "pentest"]'),
('encryption', 'security', '["encryption", "cryptography"]'),
('owasp', 'security', '["owasp", "owasp top 10"]'),
('sso', 'security', '["sso", "single sign-on", "single sign on"]'),
('network security', 'security', '["network security"]');
