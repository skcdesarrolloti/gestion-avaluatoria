<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, HttpException, Session};
use App\Models\{AnalystAccountRepository, AppraiserRepository, FuncionarioRepository};
final class AnalystAccessController
{
    public function __construct(private AnalystAccountRepository $accounts, private AppraiserRepository $experts, private FuncionarioRepository $staff, private array $user) {}
    public function create(): never {
        $this->ownerOnly();
        Session::flash('access_values',json_encode(['username'=>$this->text('username'),'full_name'=>$this->text('full_name'),'appraiser_id'=>$this->text('appraiser_id')],JSON_UNESCAPED_UNICODE));
        try {
            $login=strtolower(trim($this->text('username'))); $name=trim($this->text('full_name')); $expert=$this->text('appraiser_id');
            $existing=$this->accounts->byLogin($login);
            if ($existing && (int)$existing['owner_id'] === (int)$this->user['id']) {
                throw new HttpException(422, $existing['active']
                    ? 'Este analista ya está creado en tu cuenta y está activo. Consulta Accesos registrados debajo del formulario. No necesitas crearlo otra vez; su contraseña no se restablece aquí.'
                    : 'Este analista ya está creado en tu cuenta, pero está desactivado. No se puede habilitar creando otra vez el mismo usuario.');
            }
            if ($this->staff->hasLogin($login)) throw new HttpException(422,'Este usuario ya existe en el acceso compartido de funcionarios de SuCasa. No se creó un acceso de analista ni se cambió su contraseña. Su administrador debe revisar la cuenta existente; este formulario no la vincula automáticamente.');
            if ($existing) throw new HttpException(422,'Ese nombre de usuario no está disponible para crear un analista en tu cuenta. No se guardó este acceso.');
            $valid=array_column($this->experts->eligibleForAssignment(),'id');
            if (!in_array($expert,$valid,true)) throw new HttpException(422,'Selecciona tu perito responsable con RAA vigente.');
            $this->accounts->create($this->user['id'],$expert,$name,$login);
            Session::pullFlash('access_values');
            Session::flash('masters_message','Acceso creado. La contraseña inicial es el usuario y debe cambiarse al primer ingreso. Sus nuevos avalúos aparecerán también en tu cuenta.');
        } catch (HttpException $e) { Session::flash('masters_error',$e->getMessage()); }
        catch (\PDOException) { Session::flash('masters_error','No se creó el acceso. Comprueba si el usuario ya existe o hay migraciones pendientes.'); }
        Http::redirect('maestros?tab=access#acceso-analista');
    }
    public function revoke(string $id): never {
        $this->ownerOnly(); $this->accounts->revoke($id,$this->user['id']);
        Session::flash('masters_message','Acceso desactivado. Los avalúos y soportes se conservan.'); Http::redirect('maestros?tab=access#acceso-analista');
    }
    public function password(): void {
        if (empty($this->user['analyst_id'])) throw new HttpException(403,'Esta pantalla corresponde al acceso del analista.');
        view('auth/analyst-password',['title'=>'Cambiar contraseña inicial','error'=>Session::pullFlash('access_error')]);
    }
    public function changePassword(): never {
        if (empty($this->user['analyst_id'])) throw new HttpException(403,'Acceso no permitido.');
        try {
            $password=$this->text('password');
            if ($password !== $this->text('confirmation')) throw new HttpException(422,'Las contraseñas no coinciden.');
            $this->accounts->changePassword($this->user['analyst_id'],$this->user['auth_version'],$password);
            Session::logout(); Http::redirect('login');
        } catch (HttpException $e) { Session::flash('access_error',$e->getMessage()); Http::redirect('acceso/clave'); }
    }
    private function ownerOnly(): void { if (!empty($this->user['analyst_id'])) throw new HttpException(403,'Solo el titular administra accesos.'); }
    private function text(string $key): string { return is_string($_POST[$key]??null) ? $_POST[$key] : ''; }
}
