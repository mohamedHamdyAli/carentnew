require 'jwt'

key_file = 'key.txt'
team_id = '7D6T5X85L3'
client_id = 'com.nahrdev.carent'
key_id = 'JLKPL7PQ9Y'

ecdsa_key = OpenSSL::PKey::EC.new IO.read key_file

headers = {
  'kid' => key_id
}

claims = {
	'iss' => team_id,
	'iat' => Time.now.to_i,
	'exp' => Time.now.to_i + 86400*180,
	'aud' => 'https://appleid.apple.com',
	'sub' => client_id,
}

token = JWT.encode claims, ecdsa_key, 'ES256', headers

puts token