@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2 style="font-weight: 700; color: #1e293b; margin: 0;">Gestion des Fournisseurs</h2>
        <p style="color: #64748b; margin: 5px 0 0 0; font-size: 14px;">Liste complète des fournisseurs et leurs coordonnées</p>
    </div>
    
    @if(auth()->user()->canAccess($screen, 'create'))
        <button class="red" style="border:none; padding:11px 22px; color: white; background-color: #C1121F; border-radius:8px; cursor:pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(193, 18, 31, 0.2); transition: all 0.2s;" onclick="openAddModal()">
            <i class="fa-solid fa-plus"></i> Ajouter Fournisseur
        </button>
    @endif
</div>

<div style="margin-bottom: 22px; display: flex; max-width: 400px; position: relative;">
    <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b;">
        <i class="fa-solid fa-magnifying-glass"></i>
    </span>
    <input type="text" id="fournisseurSearchBar" onkeyup="filterFournisseurTable()" placeholder="Rechercher par code, nom, téléphone, contact..." 
           style="width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; box-shadow: 0 1px 3px rgba(0,0,0,0.02); transition: all 0.2s;"
           onfocus="this.style.borderColor='#C1121F'; this.style.boxShadow='0 0 0 3px rgba(193, 18, 31, 0.1)';" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
</div>

@if(session('success'))
    <div style="background-color: #DCFCE7; color: #15803D; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border-left: 4px solid #15803D; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif
<!-- حاوية الجدول المحسنة تمنع التمرير وتتكيف 100% مع عرض الصفحة المتاح -->
<div class="table-card" style="width: 100%; background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden;">
    <table id="fournisseurTable" style="width: 100%; border-collapse: collapse; text-align: left; table-layout: fixed;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <!-- توزيع النسب المئوية بدقة لضمان ملاءمة تامة لكل الشاشات -->
                <th style="padding: 16px 20px; color: #ffffff; font-weight: 700; font-size: 13px; text-transform: uppercase; width: 28%;">Fournisseur / Code</th>
                <th style="padding: 16px 20px; color: #ffffff; font-weight: 700; font-size: 13px; text-transform: uppercase; width: 24%;">Filiale & Adresse</th>
                <th style="padding: 16px 20px; color: #ffffff; font-weight: 700; font-size: 13px; text-transform: uppercase; width: 28%;">Contact & Télécommunications</th>
                <th style="padding: 16px 20px; color: #ffffff; font-weight: 700; font-size: 13px; text-transform: uppercase; width: 10%; text-align: center;">Statut</th>
                <th style="padding: 16px 20px; color: #ffffff; font-weight: 700; font-size: 13px; text-transform: uppercase; width: 10%; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fournisseurs as $fournisseur)
            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                
                <!-- عمود المورد -->
                <td style="padding: 16px 20px; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word;">
                    <div style="font-weight: 700; color: #1e293b; font-size: 15px; margin-bottom: 4px; line-height: 1.2;">{{ $fournisseur->nom_fournisseur }}</div>
                    <span style="background: #e2e8f0; color: #475569; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; display: inline-block;">{{ $fournisseur->code_fournisseur }}</span>
                    @if($fournisseur->remarque)
                        <div style="margin-top: 8px; font-size: 12px; color: #64748b; font-style: italic; max-width: 100%; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $fournisseur->remarque }}">
                            <i class="fa-regular fa-comment-dots"></i> {{ $fournisseur->remarque }}
                        </div>
                    @endif
                </td>

                <!-- عمود الفرع والعنوان -->
                <td style="padding: 16px 20px; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word;">
                    <div style="margin-bottom: 8px;">
                        <span style="color: #0284c7; background: #f0f9ff; border: 1px solid #bae6fd; padding: 3px 8px; border-radius: 6px; font-weight: 600; font-size: 11px; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fa-solid fa-sitemap"></i> {{ $fournisseur->filiale->nom_filiale ?? 'Non rattaché' }}
                        </span>
                    </div>
                    <div style="font-size: 13px; color: #64748b; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $fournisseur->adresse }}">
                        <i class="fa-solid fa-location-dot" style="color: #94a3b8; width: 14px;"></i> {{ $fournisseur->adresse ?? 'Adresse non spécifiée' }}
                    </div>
                </td>

                <!-- عمود وسائل الاتصال المدمج -->
                <td style="padding: 16px 20px; vertical-align: top; font-size: 13px; color: #334155; word-wrap: break-word; overflow-wrap: break-word;">
                    @if($fournisseur->contact)
                        <div style="font-weight: 600; color: #1e293b; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-user-tie" style="color: #64748b;"></i> {{ $fournisseur->contact }}
                        </div>
                    @endif
                    <div style="display: flex; flex-direction: column; gap: 4px; color: #475569;">
                        @if($fournisseur->telephone)
                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="fa-solid fa-phone" style="color: #94a3b8; width: 16px;"></i> {{ $fournisseur->telephone }}</div>
                        @endif
                        @if($fournisseur->email)
                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="fa-solid fa-envelope" style="color: #94a3b8; width: 16px;"></i> <a href="mailto:{{ $fournisseur->email }}" style="color: #0284c7; text-decoration: none;" title="{{ $fournisseur->email }}">{{ $fournisseur->email }}</a></div>
                        @endif
                        @if($fournisseur->fax)
                            <div style="font-size: 12px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="fa-solid fa-print" style="color: #cbd5e1; width: 16px;"></i> Fax: {{ $fournisseur->fax }}</div>
                        @endif
                        @if(!$fournisseur->telephone && !$fournisseur->email && !$fournisseur->fax && !$fournisseur->contact)
                            <span style="color: #cbd5e1; font-style: italic;">Aucun contact</span>
                        @endif
                    </div>
                </td>

                <!-- حالة الحساب -->
                <td style="padding: 16px 20px; text-align: center; vertical-align: middle;">
                    @if($fournisseur->fournisseur_bloque)
                        <span style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #FEE2E2; color: #991B1B; border: 1px solid #fca5a5; white-space: nowrap;">
                            <span style="height: 6px; width: 6px; background-color: #b91c1c; border-radius: 50%;"></span> Bloqué
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #DCFCE7; color: #15803D; border: 1px solid #86efac; white-space: nowrap;">
                            <span style="height: 6px; width: 6px; background-color: #16a34a; border-radius: 50%;"></span> Actif
                        </span>
                    @endif
                </td>

                <!-- الإجراءات -->
                <td style="padding: 16px 20px; text-align: center; vertical-align: middle;">
                    <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                        @if(auth()->user()->canAccess($screen, 'edit'))
                            <button type="button" class="warning" style="border:none; cursor:pointer; font-size:14px; padding: 8px 12px; background: #f59e0b; color: white; border-radius: 6px; transition: background 0.15s;" onmouseover="this.style.background='#d97706'" onmouseout="this.style.background='#f59e0b'" onclick="openEditModal({{ json_encode($fournisseur) }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        @endif

                        @if(auth()->user()->canAccess($screen, 'delete'))
                            <form action="{{ route('referentiels.fournisseurs.destroy', $fournisseur->id) }}" method="POST" onsubmit="return confirm('Supprimer ce fournisseur ?')" style="margin:0; display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="danger" style="border:none; cursor:pointer; font-size:14px; padding: 8px 12px; background: #dc2626; color: white; border-radius: 6px; transition: background 0.15s;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; color: #94a3b8; padding: 40px; font-size: 15px;">
                    <i class="fa-solid fa-folder-open" style="font-size: 32px; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                    Aucun fournisseur enregistré.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<!-- الخلفية -->
<div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0, 0, 0, 0.4); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    
    <!-- الحاوية بعرض أكبر قليلاً وارتفاع مثالي ومحكم يمنع الحاجة للتمرير -->
    <div style="background:white; width:100%; max-width:650px; max-height: 150vh; padding: 35px 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; box-sizing: border-box;">
        
        <h2 style="margin: 0 0 18px 0; font-family: sans-serif; font-size: 20px; color: #1e293b; font-weight: bold;">Ajouter un Fournisseur</h2>

        <form action="{{ route('referentiels.fournisseurs.store') }}" method="POST" style="display:flex; flex-direction:column; gap: 12px; margin: 0;">
            @csrf
            
            <!-- الصف الأول: كود المورد + الاسم -->
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Code Fournisseur</label>
                    <input type="text" name="code_fournisseur" placeholder="Ex: FRN-001" required 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Nom / Raison Sociale</label>
                    <input type="text" name="nom_fournisseur" required 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
            </div>

            <!-- الصف الثاني: الفرع (ممتد بكامل العرض كعنصر رئيسي متناسق) -->
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Rattaché à la Filiale</label>
                <select name="filiale_id" required 
                        style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: white; width: 100%; box-sizing: border-box;">
                    <option value="" disabled selected>Sélectionnez une filiale</option>
                    @foreach($filiales as $filiale)
                        <option value="{{ $filiale->id }}">{{ $filiale->nom_filiale }}</option>
                    @endforeach
                </select>
            </div>

            <!-- الصف الثالث: الهاتف + الفاكس -->
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Téléphone</label>
                    <input type="text" name="telephone" 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Fax</label>
                    <input type="text" name="fax" 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
            </div>

            <!-- الصف الرابع: البريد الإلكتروني + اسم جهة الاتصال -->
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Email</label>
                    <input type="email" name="email" 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Nom du Contact</label>
                    <input type="text" name="contact" 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
            </div>

            <!-- الصف الخامس: العنوان + الملاحظة (جنباً إلى جنب لتوفير سطر كامل عمودياً) -->
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Adresse</label>
                    <input type="text" name="adresse" 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Remarque</label>
                    <input type="text" name="remarque" 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
            </div>

            <!-- أزرار التحكم -->
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="closeAddModal()" 
                        style="background: #f1f5f9; color: #475569; border: none; padding: 11px 20px; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 13px;">
                    Annuler
                </button>
                <button type="submit" 
                        style="background: #C1121F; color: white; border: none; padding: 11px 20px; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 13px;">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
<!-- الخلفية -->
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0, 0, 0, 0.4); z-index:999; align-items:center; justify-content:center; padding: 20px; box-sizing: border-box;">
    
    <!-- الحاوية بعرض أكبر قليلاً وارتفاع مثالي ومحكم يمنع الحاجة للتمرير -->
    <div style="background:white; width:100%; max-width:720px; padding: 25px 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; box-sizing: border-box;">
        
        <h2 style="margin: 0 0 18px 0; font-family: sans-serif; font-size: 20px; color: #1e293b; font-weight: bold;">Modifier le Fournisseur</h2>

        <form id="editForm" method="POST" style="display:flex; flex-direction:column; gap: 12px; margin: 0;">
            @csrf
            @method('PUT')
            
            <!-- الصف الأول: كود المورد + الاسم -->
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Code Fournisseur</label>
                    <input type="text" name="code_fournisseur" id="edit_code" required 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Nom / Raison Sociale</label>
                    <input type="text" name="nom_fournisseur" id="edit_nom" required 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
            </div>

            <!-- الصف الثاني: الفرع + حالة الحساب (دمجناهما معاً للحفاظ على الارتفاع) -->
            <div style="display: flex; gap: 15px;">
                <div style="flex: 2; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Rattaché à la Filiale</label>
                    <select name="filiale_id" id="edit_filiale" required 
                            style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: white; width: 100%; box-sizing: border-box;">
                        @foreach($filiales as $filiale)
                            <option value="{{ $filiale->id }}">{{ $filiale->nom_filiale }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Statut</label>
                    <select name="fournisseur_bloque" id="edit_bloque" required 
                            style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: white; width: 100%; box-sizing: border-box;">
                        <option value="0">Actif</option>
                        <option value="1">Bloqué</option>
                    </select>
                </div>
            </div>

            <!-- الصف الثالث: الهاتف + الفاكس -->
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Téléphone</label>
                    <input type="text" name="telephone" id="edit_telephone" 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Fax</label>
                    <input type="text" name="fax" id="edit_fax" 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
            </div>

            <!-- الصف الرابع: البريد الإلكتروني + اسم جهة الاتصال -->
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Email</label>
                    <input type="email" name="email" id="edit_email" 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Nom du Contact</label>
                    <input type="text" name="contact" id="edit_contact" 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
            </div>

            <!-- الصف الخامس: العنوان + الملاحظة -->
            <div style="display: flex; gap: 15px;">
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Adresse</label>
                    <input type="text" name="adresse" id="edit_adresse" 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 13px; color: #1e293b; font-weight: 600;">Remarque</label>
                    <input type="text" name="remarque" id="edit_remarque" 
                           style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #fff; width: 100%; box-sizing: border-box;">
                </div>
            </div>

            <!-- أزرار التحكم -->
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="closeEditModal()" 
                        style="background: #f1f5f9; color: #475569; border: none; padding: 11px 20px; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 13px;">
                    Annuler
                </button>
                <button type="submit" 
                        style="background: #f59e0b; color: white; border: none; padding: 11px 20px; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 13px;">
                    Modifier
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() { document.getElementById('addModal').style.display = 'flex'; }
function closeAddModal() { document.getElementById('addModal').style.display = 'none'; }

function openEditModal(f) {
    document.getElementById('editForm').action = '/referentiels/fournisseurs/' + f.id;
    document.getElementById('edit_code').value = f.code_fournisseur;
    document.getElementById('edit_nom').value = f.nom_fournisseur;
    document.getElementById('edit_filiale').value = f.filiale_id;
    document.getElementById('edit_telephone').value = f.telephone ? f.telephone : '';
    document.getElementById('edit_fax').value = f.fax ? f.fax : '';
    document.getElementById('edit_email').value = f.email ? f.email : '';
    document.getElementById('edit_contact').value = f.contact ? f.contact : '';
    document.getElementById('edit_adresse').value = f.adresse ? f.adresse : '';
    document.getElementById('edit_remarque').value = f.remarque ? f.remarque : '';
    document.getElementById('edit_bloque').value = f.fournisseur_bloque;
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }

// 🔍 تحديث دالة البحث لتتوافق مع التركيبة المدمجة الجديدة للأعمدة
function filterFournisseurTable() {
    let input = document.getElementById("fournisseurSearchBar");
    let filter = input.value.toLowerCase().trim();
    let table = document.getElementById("fournisseurTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let tdInfo = tr[i].getElementsByTagName("td")[0];      // الاسم والكود والملاحظة
        let tdFiliale = tr[i].getElementsByTagName("td")[1];   // الفرع والعنوان
        let tdContact = tr[i].getElementsByTagName("td")[2];   // الاتصال والإيميل والهاتف

        if (tdInfo || tdFiliale || tdContact) {
            let infoText = tdInfo.textContent || tdInfo.innerText;
            let filialeText = tdFiliale.textContent || tdFiliale.innerText;
            let contactText = tdContact.textContent || tdContact.innerText;

            if (
                infoText.toLowerCase().indexOf(filter) > -1 || 
                filialeText.toLowerCase().indexOf(filter) > -1 ||
                contactText.toLowerCase().indexOf(filter) > -1
            ) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>
@endsection