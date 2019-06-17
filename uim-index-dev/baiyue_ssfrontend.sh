#!/bin/sh
skip=44

tab='	'
nl='
'
IFS=" $tab$nl"

umask=`umask`
umask 77

gztmpdir=
trap 'res=$?
  test -n "$gztmpdir" && rm -fr "$gztmpdir"
  (exit $res); exit $res
' 0 1 2 3 5 10 13 15

if type mktemp >/dev/null 2>&1; then
  gztmpdir=`mktemp -dt`
else
  gztmpdir=/tmp/gztmp$$; mkdir $gztmpdir
fi || { (exit 127); exit 127; }

gztmp=$gztmpdir/$0
case $0 in
-* | */*'
') mkdir -p "$gztmp" && rm -r "$gztmp";;
*/*) gztmp=$gztmpdir/`basename "$0"`;;
esac || { (exit 127); exit 127; }

case `echo X | tail -n +1 2>/dev/null` in
X) tail_n=-n;;
*) tail_n=;;
esac
if tail $tail_n +$skip <"$0" | gzip -cd > "$gztmp"; then
  umask $umask
  chmod 700 "$gztmp"
  (sleep 5; rm -fr "$gztmpdir") 2>/dev/null &
  "$gztmp" ${1+"$@"}; res=$?
else
  echo >&2 "Cannot decompress $0"
  (exit 127); res=127
fi; exit $res
���]sspanel前端网站搭建DONE.sh �Z{SG����;���þH� ��8�ǥ*q����+�'iK���
LU������#&qNl�y�|{g%��p=3���c%�8U'��t��{z��{v����D�'��57�ۧ�M�����3Zv)�x;���]���`B݃����7��� ��N��Ƌ�Wݨ��Gj�$w���'d}�����[��o9���3ސ;�L���%z:ч=Aa0��B���j\��8�I�8����#��>�J�`��}T�1V8��Oe�	�0�������3�q��#}&����[x{+�Yɥ���|�����/��Tko�~L�[rc�����P$-�{�>�E@e�V����C�����_����5d <�0D�K	LC��L!��!d�`�_&�LƐiE�h)�ݎ\�ϐ���*�+ʚ��T	A��1���vvs��y�R������W$`2�s�	T]�;3T�Ӽ!���z7�_�<D��,����,�g���_HT��Emsol��W�?���M��</"���2�v��-mm'�Zc.���#��z}��q�C�{e)!�K��[�uX0�d�"٧DRH�Jr@ID,������s1"u���*T>����#�'��* ���Y�d�Yf��������#߷B�A��xU�0�ķ�3�s�^��e��,�'����"	1�
��z�	���皛���P��<<2F�=Ƭ�Őۇ������ Z��u�ho;��
lNJ�cwT���p{#�|4*8��5�0�'����p؇'������gG���v��b�ʙhB�e1�%����A��m����x���ܭ9��TsSww<�(]�^u��!B�I�y�����IaOB��݇��2lt��o��}<�����)$��@(
ܕ6�ϑe4����rk���K/�ƗVY��c�700@~eIR9� ߣ�h�^9�<r	b�?��ScQi �a�$�L���V���B�dX1�����xi�d?O���/�#����TC��DP�`4�*� Ϡ��Ƅ�g��̴>�2�)Q��#�i��1��f����S�Գ��������i��	߻ 9Anu
o�2VC�@/�"�1��'�ǇN{B��
!T�J����%q��O��?��C}B?�E@Q�`�!���\t}#c$�~t yO��{�@�i���ޒ)c	�!�s����a�8x�?��jqe����k��Ѳ�H8��pfӔÇ�G@�(�B����9�a�h���pp>�WC��+�B9g\�B)΋{f�i�Š&��@�!j	!� �����H<�N�T�J<�d�UP�J���4�^�/�z��J��;x�~1#���RZ+B7�$�ȓp��{䉳�$9��%q�����	b ��� ��|?'& x�y��dp�B�L�AM{��?�V�N��b�π�<� ��;�:`��:%=Zm58�~��Jr���(�KBT\����������iV�Z:�^A<���\H�ԓ#��t~7�_������BK��,gJ�JnI���s�<�Q^A~wB�7�8����~���r��w���î#���9��O��&�2	�f��\I.��n�U����A=\�e���x�'J֮�Q65��d��f�z�0��Û��M�������ʌ/oA��?BdV�����^�=U�)^+!G������>����{<�'A�+�oH�r������ŕ���G�𺓿J�>?���C
b 2� E���3�@�%ċ�դ8�6��J�]T�5y�-�K�aw��^��_m{_Z��^?�&6��ւZ[!�#?$lW��Y�0u���BP�2(D]�D����)qI�r$�����ؠ=b-�0�~'�4|���u�������!J�:��BNzITB<�S�P�f(�Nn�pX�7{k�-�-\���u�(]����v@����~�����b�7lf}�_�� {Y1���g��W����h�`�V��i�y��3�W;�8y2/mk^����#%P��(�e�����i�F��%-{T�Skx{�0y}Z���'��$=�zќ��X9��?��?�p�.l��g���a$r��ع�U}�Nͼ�Y�c��8�^�Ɩ�����!Ge'���$9m�g�l3�����߽靶���7�_�$���#�NvV����A��i���>N�=(<����"��)��Ni\��=��K[�	R��ZM�Aؽ���~�n�**����]8��s����ep�{@�m��a�ǈ��)�������W;��)��hg�$�FI�g7����G����$�*=jL�/����M�Q�n42�A�JT�GW��2{֜G���(լ4r�و�FB��k���Kn�An{��έV���L�[��	�z�ӅX�n�R�ܬ��k&�~�r1������O�ָ)l���i c�"V����6dv&b5�����a뾵�F4|�b���X��H�| K�\3r�ϟT����T�G�C�a�������ڂkJ�`ȶ��>��b�����Ol����&ʯ����V���5�X�IpU]$�	�/R�*Wɩ}���b�y@�.R�T�+�3�4�ԠRX��:�9yŦXyל��e���2��/���Vg~�a����H��%N��Ԁ��U�/U��#��i-����\>Ws� uɄ�֚�i� mk��׃(�X]ƙ�\q{��8hl+ƓD���oM�Ù�Ģq0����$���6{��2�� ky��_�����er����c0��m�����j[��[1�R���r0��]�.�c �^�A��y�����b�/�Z�![����?��_���i+w3�>c��T�kŧy���[3� ���d`7��W�ĸ��P�w�BVF��J�=b�[J�����P���W�73�InP�nj�]����>��|�\2�#Z����~֘��������4�3P��f�;VXنZˎ�v��g�0�ҧ��d���:Ps��#	����i�����_���S:Z4��Ŭ@f��w/�ܬ0�>XG���F.cvS�Y಻�|^��G�}�E1X�#�k/j��QRI�����2D�z���Ǡ�5��+ ���e�2sP���TQ�b�	1��2�o"�r�|Ė(� y{Ę*��]6	Ǐvw��c�p��:e�Y�)�%DЉ�}H�zˏ��.�f�/��'��Z�����,�g
��t	���7��59]���N���jAV_��[�җ��0�w���D�Ya��,�ц�9��~�_�g�P̯��#�И���'7�dM�|�O\�gv�3f ��'~V4%kÉ�ʵ��'���1����,9�ۘ�36YqYՈ�����PB�"ϧ�.�tF�Ő��JK���%�(�)R�Z��g{(o=������Y�	�ϴ����Q���KB� ���� �+  